<?php
session_start();
require_once '../Authentication/db.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($property_id == 0) die("Property ID not specified");

$query = "SELECT p.*, u.fullname, u.phone, u.email 
          FROM properties p 
          LEFT JOIN users u ON p.owner_id = u.user_id 
          WHERE p.id = ? AND p.status = 'approved'";
$stmt = $pdo->prepare($query);
$stmt->execute([$property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$property) die("Property not found");

$property_images = [];
if(!empty($property['image'])) {
    $image_urls = explode(',', $property['image']);
    foreach($image_urls as $img_url) {
        $trimmed_url = trim($img_url);
        if(!empty($trimmed_url)) {
            $property_images[] = ['image_url' => $trimmed_url];
        }
    }
}

$google_maps_js_key    = "Your API Key";
$google_geocoding_key  = "Your API Key";

function getSmartCoordinates($location_name, $api_key) {
    $search_query = urlencode($location_name . ', Somaliland');
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$search_query}&key={$api_key}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    if($data['status'] === 'OK') {
        return ['lat'=>$data['results'][0]['geometry']['location']['lat'],'lng'=>$data['results'][0]['geometry']['location']['lng'],'formatted_address'=>$data['results'][0]['formatted_address'],'source'=>'google_maps','accuracy'=>'precise'];
    }
    $lower_location = strtolower(trim($location_name));
    $hargeisa_areas = [
        'ibrahim kodbur'=>['lat'=>9.5710,'lng'=>44.0580],'new hargeisa'=>['lat'=>9.5680,'lng'=>44.0620],
        'ahmed dhagah'=>['lat'=>9.5740,'lng'=>44.0650],'mohamud haybe'=>['lat'=>9.5700,'lng'=>44.0700],
        'masalaha'=>['lat'=>9.5660,'lng'=>44.0550],'jigjiga yar'=>['lat'=>9.5630,'lng'=>44.0600],
        'gacan libaax'=>['lat'=>9.5720,'lng'=>44.0630],'daami'=>['lat'=>9.5750,'lng'=>44.0590],
        'hodan'=>['lat'=>9.5650,'lng'=>44.0640],'koodbuur'=>['lat'=>9.5710,'lng'=>44.0580],
        'goljano'=>['lat'=>9.5600,'lng'=>44.0680],'sheikh'=>['lat'=>9.5580,'lng'=>44.0720],
        'mansoor'=>['lat'=>9.5530,'lng'=>44.0660],'daloodho'=>['lat'=>9.5690,'lng'=>44.0670],
        'dab la taagay'=>['lat'=>9.5725,'lng'=>44.0615],'xidigta'=>['lat'=>9.5770,'lng'=>44.0690],
    ];
    foreach($hargeisa_areas as $area=>$coords) {
        if(strpos($lower_location,$area)!==false) {
            return ['lat'=>$coords['lat'],'lng'=>$coords['lng'],'formatted_address'=>ucwords($area).', Hargeisa, Somaliland','source'=>'hargeisa_db','accuracy'=>'area_match'];
        }
    }
    return ['lat'=>9.56+(mt_rand(0,200)/10000),'lng'=>44.06+(mt_rand(0,200)/10000),'formatted_address'=>$location_name.', Hargeisa, Somaliland','source'=>'dynamic_random','accuracy'=>'approximate'];
}

$location_data = getSmartCoordinates($property['location'], $google_geocoding_key);
$latitude      = $location_data['lat'];
$longitude     = $location_data['lng'];
$full_address  = $location_data['formatted_address'];
$coord_source  = $location_data['source'];
$accuracy      = $location_data['accuracy'];

$clean_phone   = preg_replace('/[^0-9]/', '', $property['phone']);
$whatsapp_link = $clean_phone ? "https://wa.me/{$clean_phone}" : '#';
$tel_link      = $clean_phone ? "tel:{$clean_phone}" : '#';
$email_link    = $property['email'] ? "mailto:{$property['email']}?subject=Property Inquiry: ".urlencode($property['title']) : '#';

$image_count   = count($property_images);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($property['title']); ?> — HoyLink</title>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_maps_js_key; ?>&libraries=places,geometry"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* ─── TOKENS ─────────────────────────────────── */
:root{
  --bg:       #0b1120;
  --surface:  #111827;
  --surface2: #1a2236;
  --border:   rgba(255,255,255,0.07);
  --blue:     #3b82f6;
  --blue-dim: rgba(59,130,246,0.12);
  --green:    #10b981;
  --wa:       #25D366;
  --wa-dark:  #128C7E;
  --red:      #ef4444;
  --text:     #f1f5f9;
  --muted:    #64748b;
  --light:    #94a3b8;
  --radius:   14px;
  --shadow:   0 8px 32px rgba(0,0,0,0.45);
}
*{margin:0;padding:0;box-sizing:border-box;}
html{scroll-behavior:smooth;}
body{background:var(--bg);color:var(--text);font-family:'Segoe UI',system-ui,sans-serif;line-height:1.6;padding-bottom:90px;}

/* ─── TOPBAR ──────────────────────────────────── */
.topbar{
  position:sticky;top:0;z-index:200;
  background:rgba(11,17,32,0.85);
  backdrop-filter:blur(16px);
  -webkit-backdrop-filter:blur(16px);
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
  padding:12px 20px;
}
.logo{display:flex;align-items:center;gap:10px;font-size:19px;font-weight:700;letter-spacing:-0.3px;}
.logo span{color:var(--blue);}
.back-btn{
  display:inline-flex;align-items:center;gap:7px;
  padding:8px 16px;border-radius:8px;
  background:var(--blue-dim);border:1px solid rgba(59,130,246,0.25);
  color:#93c5fd;font-size:13px;font-weight:600;text-decoration:none;
  transition:all .2s;
}
.back-btn:hover{background:rgba(59,130,246,0.2);transform:translateX(-2px);}

/* ─── HERO SLIDER ─────────────────────────────── */
.hero-slider{
  position:relative;
  height:480px;
  background:var(--surface);
  overflow:hidden;
}
@media(max-width:640px){.hero-slider{height:300px;}}

.slides-track{
  display:flex;height:100%;
  transition:transform .45s cubic-bezier(.4,0,.2,1);
}
.slide{
  flex:0 0 100%;height:100%;position:relative;
  background:#0b1120;
}
.slide img{
  width:100%;height:100%;object-fit:cover;
  display:block;
}
.slide-overlay{
  position:absolute;inset:0;
  background:linear-gradient(to top,rgba(5,8,18,.92) 0%,rgba(5,8,18,.3) 55%,transparent 100%);
}

/* Hero info on top of slider */
.hero-info{
  position:absolute;bottom:0;left:0;right:0;
  padding:28px 28px 22px;
  z-index:10;
}
.hero-badge{
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(59,130,246,0.18);border:1px solid rgba(59,130,246,0.35);
  color:#93c5fd;border-radius:20px;padding:4px 12px;font-size:11px;font-weight:700;
  letter-spacing:.8px;text-transform:uppercase;margin-bottom:10px;
}
.hero-title{
  font-size:clamp(20px,4vw,30px);font-weight:800;
  line-height:1.2;margin-bottom:10px;
  text-shadow:0 2px 12px rgba(0,0,0,.6);
}
.hero-meta{
  display:flex;align-items:center;gap:14px;flex-wrap:wrap;
  font-size:13px;color:var(--light);margin-bottom:14px;
}
.hero-meta i{color:var(--blue);font-size:11px;}
.hero-price{
  display:inline-block;
  background:linear-gradient(135deg,var(--green),#059669);
  padding:8px 20px;border-radius:8px;
  font-size:22px;font-weight:800;color:#fff;
  box-shadow:0 4px 16px rgba(16,185,129,.35);
}

/* Slider arrows */
.slider-arrow{
  position:absolute;top:50%;transform:translateY(-50%);
  z-index:20;width:42px;height:42px;border-radius:50%;
  background:rgba(11,17,32,.7);border:1px solid var(--border);
  color:#fff;font-size:14px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  transition:all .2s;backdrop-filter:blur(8px);
}
.slider-arrow:hover{background:var(--blue);border-color:var(--blue);}
.arrow-prev{left:16px;}
.arrow-next{right:16px;}
@media(max-width:480px){.slider-arrow{width:34px;height:34px;font-size:12px;}}

/* Dots */
.slider-dots{
  position:absolute;bottom:80px;left:50%;transform:translateX(-50%);
  display:flex;gap:6px;z-index:20;
}
@media(max-width:640px){.slider-dots{bottom:60px;}}
.dot{
  width:7px;height:7px;border-radius:50%;
  background:rgba(255,255,255,.35);border:none;cursor:pointer;
  transition:all .25s;padding:0;
}
.dot.active{width:22px;border-radius:4px;background:var(--blue);}

/* Slide count badge */
.slide-count{
  position:absolute;top:16px;right:16px;z-index:20;
  background:rgba(11,17,32,.75);backdrop-filter:blur(8px);
  border:1px solid var(--border);border-radius:20px;
  padding:4px 12px;font-size:12px;color:var(--light);
}

/* No image placeholder */
.no-img-hero{
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  height:100%;gap:12px;color:var(--muted);
}
.no-img-hero i{font-size:52px;opacity:.35;}

/* ─── STICKY CONTACT BAR (mobile) ─────────────── */
.sticky-bar{
  position:fixed;bottom:0;left:0;right:0;z-index:300;
  background:rgba(11,17,32,.96);
  backdrop-filter:blur(20px);
  border-top:1px solid var(--border);
  display:flex;gap:10px;padding:12px 16px;
}
.sticky-btn{
  flex:1;display:flex;align-items:center;justify-content:center;gap:8px;
  padding:12px 8px;border-radius:10px;border:none;
  font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;
  transition:all .2s;
}
.sticky-btn i{font-size:16px;}
.sticky-wa{background:linear-gradient(135deg,var(--wa),var(--wa-dark));color:#fff;}
.sticky-call{background:linear-gradient(135deg,var(--blue),#1d4ed8);color:#fff;}
.sticky-share{
  flex:0 0 auto;width:48px;
  background:var(--surface2);border:1px solid var(--border);color:var(--light);
}
.sticky-btn:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.3);}

/* ─── MAIN WRAPPER ───────────────────────────── */
.wrap{max-width:900px;margin:0 auto;padding:28px 20px;}

/* ─── SECTION CARD ────────────────────────────── */
.card{
  background:var(--surface);border:1px solid var(--border);
  border-radius:var(--radius);overflow:hidden;margin-bottom:20px;
}
.card-head{
  display:flex;align-items:center;justify-content:space-between;
  padding:16px 22px;border-bottom:1px solid var(--border);
  flex-wrap:wrap;gap:10px;
}
.card-head h3{
  display:flex;align-items:center;gap:10px;
  font-size:15px;font-weight:700;letter-spacing:.2px;
}
.card-head h3 i{color:var(--blue);}
.card-body{padding:22px;}

/* ─── QUICK STATS PILLS ───────────────────────── */
.stats-row{
  display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));
  gap:12px;margin-bottom:20px;
}
.stat-pill{
  background:var(--surface2);border:1px solid var(--border);
  border-radius:12px;padding:18px 16px;text-align:center;
  transition:all .25s;
}
.stat-pill:hover{border-color:var(--blue);transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.3);}
.stat-icon{font-size:18px;color:var(--blue);margin-bottom:6px;}
.stat-val{font-size:20px;font-weight:800;color:var(--text);line-height:1;}
.stat-lbl{font-size:11px;color:var(--muted);margin-top:4px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;}

/* ─── CONTACT BUTTONS (desktop) ──────────────── */
.contact-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;}
.contact-btn{
  display:flex;align-items:center;gap:14px;
  padding:16px 18px;border-radius:12px;text-decoration:none;
  border:none;font-size:14px;font-weight:700;cursor:pointer;
  transition:all .25s;
}
.contact-btn:hover{transform:translateY(-3px);box-shadow:var(--shadow);}
.contact-btn .cb-icon{
  width:44px;height:44px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:20px;flex-shrink:0;
}
.cb-wa{background:rgba(37,211,102,.08);border:1px solid rgba(37,211,102,.2);color:var(--text);}
.cb-wa .cb-icon{background:rgba(37,211,102,.15);color:var(--wa);}
.cb-call{background:var(--blue-dim);border:1px solid rgba(59,130,246,.2);color:var(--text);}
.cb-call .cb-icon{background:rgba(59,130,246,.15);color:var(--blue);}
.cb-email{background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.2);color:var(--text);}
.cb-email .cb-icon{background:rgba(239,68,68,.12);color:var(--red);}
.cb-label{font-size:11px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;}
.cb-value{font-size:14px;font-weight:700;margin-top:1px;}

/* ─── OWNER CARD ──────────────────────────────── */
.owner-row{display:flex;align-items:center;gap:16px;margin-bottom:20px;}
.owner-avatar{
  width:58px;height:58px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--blue),#8b5cf6);
  display:flex;align-items:center;justify-content:center;
  font-size:22px;font-weight:800;color:#fff;
  box-shadow:0 0 0 3px rgba(59,130,246,.2);
}
.owner-name{font-size:18px;font-weight:700;}
.owner-tag{font-size:12px;color:var(--muted);margin-top:2px;display:flex;align-items:center;gap:5px;}
.owner-tag i{color:var(--green);font-size:10px;}

/* ─── WHATSAPP COMPOSER ───────────────────────── */
.wa-composer{
  background:linear-gradient(135deg,rgba(37,211,102,.07),rgba(18,140,126,.07));
  border:1px solid rgba(37,211,102,.18);border-radius:var(--radius);
  padding:22px;margin-bottom:20px;
}
.wa-head{display:flex;align-items:center;gap:14px;margin-bottom:18px;}
.wa-head .wa-icon{
  width:48px;height:48px;border-radius:12px;
  background:rgba(37,211,102,.15);
  display:flex;align-items:center;justify-content:center;
  font-size:22px;color:var(--wa);flex-shrink:0;
}
.wa-head h3{font-size:16px;font-weight:700;}
.wa-head p{font-size:12px;color:var(--muted);margin-top:2px;}
.wa-composer textarea{
  width:100%;min-height:120px;resize:vertical;
  background:rgba(0,0,0,.25);border:1px solid rgba(255,255,255,.08);
  border-radius:10px;padding:14px;color:var(--text);
  font-family:inherit;font-size:14px;line-height:1.6;
  transition:border-color .2s;
}
.wa-composer textarea:focus{outline:none;border-color:rgba(37,211,102,.4);}
.wa-actions{display:flex;gap:10px;margin-top:14px;}
.btn-send-wa{
  flex:1;display:flex;align-items:center;justify-content:center;gap:9px;
  padding:13px;border:none;border-radius:10px;
  background:linear-gradient(135deg,var(--wa),var(--wa-dark));
  color:#fff;font-size:14px;font-weight:700;cursor:pointer;
  transition:all .2s;
}
.btn-send-wa:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(37,211,102,.3);}
.btn-copy{
  padding:13px 18px;border-radius:10px;
  background:var(--blue-dim);border:1px solid rgba(59,130,246,.25);
  color:#93c5fd;cursor:pointer;font-size:14px;font-weight:700;
  transition:all .2s;
}
.btn-copy:hover{background:rgba(59,130,246,.2);}

/* ─── MAP ─────────────────────────────────────── */
#dynamicMap{height:380px;width:100%;}
@media(max-width:640px){#dynamicMap{height:260px;}}
.map-footer{
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;
  padding:14px 20px;background:var(--surface2);border-top:1px solid var(--border);
  font-size:13px;
}
.coords{font-family:'Courier New',monospace;color:var(--blue);font-size:12px;margin-top:3px;}
.map-actions-row{display:flex;gap:8px;}
.map-btn-sm{
  padding:7px 13px;border-radius:7px;
  background:var(--blue-dim);border:1px solid rgba(59,130,246,.25);
  color:#93c5fd;font-size:12px;font-weight:600;cursor:pointer;
  display:flex;align-items:center;gap:5px;transition:all .2s;
}
.map-btn-sm:hover{background:rgba(59,130,246,.2);}

/* ─── DESCRIPTION ─────────────────────────────── */
.desc-text{
  color:var(--light);font-size:14.5px;line-height:1.75;
  background:rgba(0,0,0,.18);border-radius:10px;padding:18px;
}

/* ─── DIVIDER ─────────────────────────────────── */
.section-label{
  font-size:11px;font-weight:700;letter-spacing:1.2px;
  text-transform:uppercase;color:var(--muted);
  margin-bottom:14px;display:flex;align-items:center;gap:10px;
}
.section-label::after{content:'';flex:1;height:1px;background:var(--border);}

/* ─── FOOTER ──────────────────────────────────── */
.page-footer{
  text-align:center;padding:22px 20px 10px;
  border-top:1px solid var(--border);
  color:var(--muted);font-size:12px;margin-top:12px;
}
.page-footer .brand{font-size:15px;font-weight:700;color:var(--light);margin-bottom:5px;}

/* ─── SCROLL REVEAL ───────────────────────────── */
.reveal{opacity:0;transform:translateY(24px);transition:opacity .5s ease,transform .5s ease;}
.reveal.visible{opacity:1;transform:none;}
</style>
</head>
<body>

<!-- ░░ TOPBAR ░░ -->
<header class="topbar">
  <div class="logo"><span>🏠</span>HoyLink</div>
  <a href="userDashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
</header>

<!-- ░░ HERO SLIDER ░░ -->
<div class="hero-slider">

  <?php if($image_count > 0): ?>
  <div class="slides-track" id="slidesTrack">
    <?php foreach($property_images as $image): 
      $img = $image['image_url'];
      if(!filter_var($img, FILTER_VALIDATE_URL) && strpos($img,'uploads/')===false) $img='uploads/'.$img;
    ?>
    <div class="slide">
      <img src="<?php echo htmlspecialchars($img); ?>"
           alt="Property"
           onerror="this.src='https://via.placeholder.com/900x480/111827/3b82f6?text=HoyLink'">
      <div class="slide-overlay"></div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Arrows -->
  <?php if($image_count > 1): ?>
  <button class="slider-arrow arrow-prev" onclick="slidePrev()"><i class="fas fa-chevron-left"></i></button>
  <button class="slider-arrow arrow-next" onclick="slideNext()"><i class="fas fa-chevron-right"></i></button>
  <div class="slider-dots" id="dotsContainer">
    <?php for($i=0;$i<$image_count;$i++): ?>
    <button class="dot<?php echo $i===0?' active':''; ?>" onclick="goToSlide(<?php echo $i; ?>)"></button>
    <?php endfor; ?>
  </div>
  <div class="slide-count" id="slideCount">1 / <?php echo $image_count; ?></div>
  <?php endif; ?>

  <?php else: ?>
  <div class="no-img-hero"><i class="fas fa-image"></i><span>No images available</span></div>
  <?php endif; ?>

  <!-- Hero Info Overlay -->
  <div class="hero-info">
    <div class="hero-badge"><i class="fas fa-check-circle"></i> Verified Property</div>
    <h1 class="hero-title"><?php echo htmlspecialchars($property['title']); ?></h1>
    <div class="hero-meta">
      <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['location']); ?></span>
      <span><i class="fas fa-hashtag"></i> HL<?php echo str_pad($property['id'],5,'0',STR_PAD_LEFT); ?></span>
      <span><i class="fas fa-calendar-alt"></i> <?php echo date('M d, Y',strtotime($property['created_at'])); ?></span>
    </div>
    <div class="hero-price">$<?php echo number_format($property['price'],2); ?></div>
  </div>
</div>

<!-- ░░ STICKY CONTACT BAR ░░ -->
<div class="sticky-bar">
  <a href="<?php echo $whatsapp_link; ?>" target="_blank" class="sticky-btn sticky-wa">
    <i class="fab fa-whatsapp"></i> WhatsApp
  </a>
  <a href="<?php echo $tel_link; ?>" class="sticky-btn sticky-call">
    <i class="fas fa-phone-alt"></i> Call Owner
  </a>
  <button class="sticky-btn sticky-share" onclick="shareProperty()" title="Share">
    <i class="fas fa-share-alt"></i>
  </button>
</div>

<!-- ░░ MAIN CONTENT ░░ -->
<div class="wrap">

  <!-- Quick Stats -->
  <div class="section-label">Property Details</div>
  <div class="stats-row reveal">
    <div class="stat-pill">
      <div class="stat-icon"><i class="fas fa-bed"></i></div>
      <div class="stat-val"><?php echo $property['bedrooms']; ?></div>
      <div class="stat-lbl">Bedrooms</div>
    </div>
    <div class="stat-pill">
      <div class="stat-icon"><i class="fas fa-bath"></i></div>
      <div class="stat-val"><?php echo $property['bathrooms']; ?></div>
      <div class="stat-lbl">Bathrooms</div>
    </div>
    <div class="stat-pill">
      <div class="stat-icon"><i class="fas fa-ruler-combined"></i></div>
      <div class="stat-val"><?php echo number_format($property['area']); ?></div>
      <div class="stat-lbl">Sq Ft</div>
    </div>
    <div class="stat-pill">
      <div class="stat-icon"><i class="fas fa-home"></i></div>
      <div class="stat-val" style="font-size:14px;"><?php echo htmlspecialchars($property['property_type'] ?? 'Villa'); ?></div>
      <div class="stat-lbl">Type</div>
    </div>
    <div class="stat-pill">
      <div class="stat-icon"><i class="fas fa-clock"></i></div>
      <div class="stat-val" style="font-size:14px;color:var(--green);">Now</div>
      <div class="stat-lbl">Available</div>
    </div>
    <div class="stat-pill">
      <div class="stat-icon"><i class="fas fa-shield-alt"></i></div>
      <div class="stat-val" style="font-size:14px;color:var(--green);">✓</div>
      <div class="stat-lbl">Verified</div>
    </div>
  </div>

  <!-- Contact the Owner -->
  <div class="section-label">Contact Owner</div>
  <div class="card reveal" style="margin-bottom:20px;">
    <div class="card-body" style="padding:20px;">
      <div class="owner-row">
        <div class="owner-avatar"><?php echo strtoupper(substr($property['fullname']?:'O',0,1)); ?></div>
        <div>
          <div class="owner-name"><?php echo htmlspecialchars($property['fullname']?:'Property Owner'); ?></div>
          <div class="owner-tag"><i class="fas fa-circle"></i> Verified Owner · HoyLink Member</div>
        </div>
      </div>
      <div class="contact-grid">
        <a href="<?php echo $whatsapp_link; ?>" target="_blank" class="contact-btn cb-wa">
          <div class="cb-icon"><i class="fab fa-whatsapp"></i></div>
          <div>
            <div class="cb-label">WhatsApp</div>
            <div class="cb-value"><?php echo htmlspecialchars($property['phone']?:'Not Available'); ?></div>
          </div>
        </a>
        <a href="<?php echo $tel_link; ?>" class="contact-btn cb-call">
          <div class="cb-icon"><i class="fas fa-phone-alt"></i></div>
          <div>
            <div class="cb-label">Direct Call</div>
            <div class="cb-value"><?php echo htmlspecialchars($property['phone']?:'Not Available'); ?></div>
          </div>
        </a>
        <a href="<?php echo $email_link; ?>" class="contact-btn cb-email">
          <div class="cb-icon"><i class="fas fa-envelope"></i></div>
          <div>
            <div class="cb-label">Email</div>
            <div class="cb-value" style="font-size:12px;word-break:break-all;"><?php echo htmlspecialchars($property['email']?:'Not Available'); ?></div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <!-- WhatsApp Composer -->
  <div class="section-label">Send Message</div>
  <div class="wa-composer reveal">
    <div class="wa-head">
      <div class="wa-icon"><i class="fab fa-whatsapp"></i></div>
      <div>
        <h3>Custom WhatsApp Message</h3>
        <p>Edit the message below then send directly</p>
      </div>
    </div>
    <textarea id="customMessage">Hi <?php echo htmlspecialchars($property['fullname']?:'Owner'); ?>, waxaan rabaa inaan gurigan gato.

Detail-ka Guriga:
🏠 Guri: <?php echo htmlspecialchars($property['title']); ?>
💰 Qiimaha: $<?php echo number_format($property['price'],2); ?>
📍 Goobta: <?php echo htmlspecialchars($property['location']); ?>
🛏 Qolalka: <?php echo $property['bedrooms']; ?> qol
🛁 Musqusha: <?php echo $property['bathrooms']; ?>
📏 Cabbirka: <?php echo number_format($property['area']); ?> sqft

Fadlan iigu sheeg detail-ka dheeraad ah. Mahadsanid!</textarea>
    <div class="wa-actions">
      <button class="btn-send-wa" onclick="sendCustomWhatsApp()">
        <i class="fab fa-whatsapp"></i> Send on WhatsApp
      </button>
      <button class="btn-copy" id="copyBtn" onclick="copyMessage()">
        <i class="fas fa-copy"></i>
      </button>
    </div>
  </div>

  <!-- Map -->
  <div class="section-label">Location</div>
  <div class="card reveal">
    <div class="card-head">
      <h3><i class="fas fa-map-marked-alt"></i> Live Property Location</h3>
      <div class="map-actions-row">
        <button class="map-btn-sm" onclick="getDirections()"><i class="fas fa-directions"></i> Directions</button>
        <button class="map-btn-sm" onclick="toggleSatellite()"><i class="fas fa-satellite"></i> Satellite</button>
      </div>
    </div>
    <div id="dynamicMap"></div>
    <div class="map-footer">
      <div>
        <div style="color:var(--light);font-size:13px;"><?php echo htmlspecialchars($full_address); ?></div>
        <div class="coords"><?php echo number_format($latitude,6); ?>, <?php echo number_format($longitude,6); ?> <span style="color:var(--green);margin-left:6px;">✓ Live</span></div>
      </div>
      <button class="map-btn-sm" onclick="shareProperty()"><i class="fas fa-share-alt"></i> Share</button>
    </div>
  </div>

  <!-- Description -->
  <div class="section-label">About This Property</div>
  <div class="card reveal">
    <div class="card-body">
      <div class="desc-text"><?php echo nl2br(htmlspecialchars($property['description']?:'No description available for this property.')); ?></div>
    </div>
  </div>

</div><!-- /wrap -->

<!-- Footer -->
<footer class="page-footer">
  <div class="brand">🏠 HoyLink Property Platform</div>
  Property ID: #HL<?php echo str_pad($property['id'],5,'0',STR_PAD_LEFT); ?> &nbsp;·&nbsp; © <?php echo date('Y'); ?> HoyLink. All rights reserved.
</footer>

<!-- ░░ SCRIPTS ░░ -->
<script>
/* ─── SLIDER ───────────────────────────────────── */
let current = 0;
const total  = <?php echo max($image_count,1); ?>;
const track  = document.getElementById('slidesTrack');
const dots   = document.querySelectorAll('.dot');
const countEl= document.getElementById('slideCount');
let autoTimer;

function goToSlide(n) {
  current = (n + total) % total;
  if(track) track.style.transform = `translateX(-${current * 100}%)`;
  dots.forEach((d,i) => d.classList.toggle('active', i === current));
  if(countEl) countEl.textContent = `${current+1} / ${total}`;
  resetAuto();
}
function slideNext(){ goToSlide(current + 1); }
function slidePrev(){ goToSlide(current - 1); }
function resetAuto(){ clearInterval(autoTimer); autoTimer = setInterval(slideNext, 4500); }
if(total > 1) resetAuto();

/* Swipe support */
let touchX = 0;
const sliderEl = document.querySelector('.hero-slider');
if(sliderEl){
  sliderEl.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; }, {passive:true});
  sliderEl.addEventListener('touchend',   e => {
    const dx = e.changedTouches[0].clientX - touchX;
    if(Math.abs(dx) > 40) dx < 0 ? slideNext() : slidePrev();
  });
}

/* ─── MAP ─────────────────────────────────────── */
let map, marker, infoWindow, isSatellite = false;
const propLoc = { lat: <?php echo $latitude; ?>, lng: <?php echo $longitude; ?> };

function initMap() {
  const dark = [
    {featureType:"all",elementType:"geometry",stylers:[{color:"#0b1120"}]},
    {featureType:"all",elementType:"labels.text.fill",stylers:[{color:"#64748b"}]},
    {featureType:"all",elementType:"labels.text.stroke",stylers:[{color:"#0b1120"}]},
    {featureType:"water",elementType:"geometry",stylers:[{color:"#111827"}]},
    {featureType:"road",elementType:"geometry",stylers:[{color:"#1e2d47"}]},
    {featureType:"road",elementType:"geometry.stroke",stylers:[{color:"#0b1120"}]},
    {featureType:"road.highway",elementType:"geometry",stylers:[{color:"#233554"}]},
    {featureType:"poi",elementType:"geometry",stylers:[{color:"#0f1d30"}]},
    {featureType:"transit",elementType:"geometry",stylers:[{color:"#0f1d30"}]},
  ];
  map = new google.maps.Map(document.getElementById("dynamicMap"), {
    center: propLoc, zoom: 15, mapTypeId:'roadmap',
    styles: dark,
    zoomControl:true, mapTypeControl:false,
    scaleControl:true, streetViewControl:true, fullscreenControl:true
  });
  marker = new google.maps.Marker({
    position: propLoc, map: map,
    title: "<?php echo addslashes($property['title']); ?>",
    icon:{path:google.maps.SymbolPath.CIRCLE,fillColor:"#3b82f6",fillOpacity:1,strokeColor:"#fff",strokeWeight:3,scale:11},
    animation: google.maps.Animation.DROP
  });
  const content = `<div style="font-family:'Segoe UI',sans-serif;padding:14px;max-width:280px;">
    <div style="font-weight:700;font-size:15px;color:#0f172a;margin-bottom:8px;"><?php echo addslashes($property['title']); ?></div>
    <div style="font-size:13px;color:#475569;margin-bottom:4px;"><b>📍</b> <?php echo addslashes($property['location']); ?></div>
    <div style="font-size:15px;font-weight:800;color:#10b981;">$<?php echo number_format($property['price'],2); ?></div>
    <div style="margin-top:8px;font-size:12px;color:#94a3b8;">🛏 <?php echo $property['bedrooms']; ?> Beds &nbsp; 🛁 <?php echo $property['bathrooms']; ?> Baths</div>
  </div>`;
  infoWindow = new google.maps.InfoWindow({content, maxWidth:290});
  marker.addListener('click', () => infoWindow.open(map, marker));
  setTimeout(() => infoWindow.open(map, marker), 1200);
  new google.maps.Circle({
    strokeColor:"#3b82f6",strokeOpacity:.25,strokeWeight:2,
    fillColor:"#3b82f6",fillOpacity:.08,
    map, center:propLoc,
    radius: <?php echo $accuracy=='precise'?50:($accuracy=='good'?100:200); ?>
  });
}

function getDirections(){
  window.open(`https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent("<?php echo addslashes($full_address); ?>")}`, '_blank','noopener');
}
function toggleSatellite(){
  isSatellite = !isSatellite;
  map.setMapTypeId(isSatellite ? 'hybrid' : 'roadmap');
}

/* ─── SHARE ─────────────────────────────────── */
function shareProperty(){
  const txt = `🏠 <?php echo addslashes($property['title']); ?>\n📍 <?php echo addslashes($property['location']); ?>\n💰 $<?php echo number_format($property['price'],2); ?>\n\n${window.location.href}`;
  if(navigator.share){ navigator.share({title:'<?php echo addslashes($property['title']); ?>',text:txt,url:window.location.href}); }
  else { navigator.clipboard.writeText(txt).then(()=>showToast('Link copied!')); }
}

/* ─── WHATSAPP ───────────────────────────────── */
function sendCustomWhatsApp(){
  const phone = "<?php echo preg_replace('/[^0-9]/','',$property['phone']); ?>";
  const msg = document.getElementById('customMessage').value;
  if(!phone){ showToast('Phone not available'); return; }
  window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
}
function copyMessage(){
  const ta = document.getElementById('customMessage');
  navigator.clipboard.writeText(ta.value).then(()=>{
    const btn = document.getElementById('copyBtn');
    btn.innerHTML = '<i class="fas fa-check"></i>';
    btn.style.color = '#10b981';
    setTimeout(()=>{ btn.innerHTML='<i class="fas fa-copy"></i>'; btn.style.color=''; }, 2000);
  });
}

/* ─── TOAST ─────────────────────────────────── */
function showToast(msg){
  const t = document.createElement('div');
  t.textContent = msg;
  Object.assign(t.style,{
    position:'fixed',bottom:'100px',left:'50%',transform:'translateX(-50%)',
    background:'#1e293b',border:'1px solid rgba(255,255,255,.1)',
    color:'#f1f5f9',padding:'10px 20px',borderRadius:'8px',
    fontSize:'13px',fontWeight:'600',zIndex:'9999',
    boxShadow:'0 8px 24px rgba(0,0,0,.4)',
    transition:'opacity .3s'
  });
  document.body.appendChild(t);
  setTimeout(()=>{ t.style.opacity='0'; setTimeout(()=>t.remove(),300); }, 2000);
}

/* ─── SCROLL REVEAL ──────────────────────────── */
const revEls = document.querySelectorAll('.reveal');
const obs = new IntersectionObserver(entries=>{
  entries.forEach(e=>{ if(e.isIntersecting) e.target.classList.add('visible'); });
},{threshold:.12});
revEls.forEach(el=>obs.observe(el));

/* ─── INIT ───────────────────────────────────── */
document.addEventListener('DOMContentLoaded', initMap);
window.addEventListener('resize',()=>{ if(map){ google.maps.event.trigger(map,'resize'); map.setCenter(propLoc); } });
</script>
</body>
</html>