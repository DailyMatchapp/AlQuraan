 <?php


header('Content-Type: text/html; charset=UTF-8');


// ==========================================
if (isset($_GET['api_proxy'])) {
    header('Content-Type: application/json; charset=utf-8');
    $target_url = filter_var($_GET['api_proxy'], FILTER_VALIDATE_URL);
    
    if ($target_url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $target_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'QuranMp3-Platform/2.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        echo $response;
    } else {
        echo json_encode(['error' => 'Invalid URL']);
    }
    exit;
}


// ==========================================
$site_title = "القرآن الكريم Mp3";
$current_year = date('Y');

$SURAH_NAMES = [
    "الفاتحة", "البقرة", "آل عمران", "النساء", "المائدة", "الأنعام", "الأعراف", "الأنفال", "التوبة", "يونس",
    "هود", "يوسف", "الرعد", "إبراهيم", "الحجر", "النحل", "الإسراء", "الكهف", "مريم", "طه",
    "الأنبياء", "الحج", "المؤمنون", "النور", "الفرقان", "الشعراء", "النمل", "القصص", "العنكبوت", "الروم",
    "لقمان", "السجدة", "الأحزاب", "سبأ", "فاطر", "يس", "الصافات", "ص", "الزمر", "غافر",
    "فصلت", "الشورى", "الزخرف", "الدخان", "الجاثية", "الأحقاف", "محمد", "الفتح", "الحجرات", "ق",
    "الذاريات", "الطور", "النجم", "القمر", "الرحمن", "الواقعة", "الحديد", "المجادلة", "الحشر", "الممتحنة",
    "الصف", "الجمعة", "المنافقون", "التغابن", "الطلاق", "التحريم", "الملك", "القلم", "الحاقة", "المعارج",
    "نوح", "الجن", "المزمل", "المدثر", "القيامة", "الإنسان", "المرسلات", "النبأ", "النازعات", "عبس",
    "التكوير", "الانفطار", "المطففين", "الانشقاق", "البروج", "الطارق", "الأعلى", "الغاشية", "الفجر", "البلد",
    "الشمس", "الليل", "الضحى", "الشرح", "التين", "العلق", "القدر", "البينة", "الزلزلة", "العاديات",
    "القارعة", "التكاثر", "العصر", "الهمزة", "الفيل", "قريش", "الماعون", "الكوثر", "الكافرون", "النصر",
    "المسد", "الإخلاص", "الفلق", "الناس"
];

$HADITH_BOOKS = [
    ['id' => 'bukhari', 'name' => 'صحيح البخاري'],
    ['id' => 'muslim', 'name' => 'صحيح مسلم'],
    ['id' => 'abudawud', 'name' => 'سنن أبي داود'],
    ['id' => 'tirmidzi', 'name' => 'جامع الترمذي'],
    ['id' => 'nasai', 'name' => 'سنن النسائي'],
    ['id' => 'ibnmajah', 'name' => 'سنن ابن ماجه']
];

$ARAB_COUNTRIES_DATA = [
    "SA" => ["name" => "السعودية", "cities" => ["مكة المكرمة", "المدينة المنورة", "الرياض", "جدة", "الدمام", "أبها", "تبوك", "حائل", "بريدة", "جازان", "نجران", "الخبر", "الظهران", "الجبيل", "الطائف", "الهفوف", "ينبع", "عرعر", "سكاكا", "الباحة", "خميس مشيط", "القطيف", "الخرج", "عنيزة", "حفر الباطن"]],
    "EG" => ["name" => "مصر", "cities" => ["القاهرة", "الإسكندرية", "الجيزة", "الأقصر", "أسوان", "المنصورة", "شرم الشيخ", "طنطا", "بور سعيد", "السويس", "الإسماعيلية", "أسيوط", "الزقازيق", "الفيوم", "دمياط", "قنا", "بني سويف", "سوهاج", "المنيا", "الغردقة", "مرسى مطروح", "كفر الشيخ", "دمنهور", "المحلة الكبرى", "العريش"]],
    "AE" => ["name" => "الإمارات", "cities" => ["أبو ظبي", "دبي", "الشارقة", "عجمان", "رأس الخيمة", "الفجيرة", "العين", "أم القيوين", "خورفكان", "كلباء", "جبل علي", "دبا الحصن", "الذيد"]],
    "KW" => ["name" => "الكويت", "cities" => ["مدينة الكويت", "الجهراء", "حولي", "الأحمدي", "الفروانية", "السالمية", "مبارك الكبير", "الفحيحيل", "العبدلي", "الوفرة"]],
    "QA" => ["name" => "قطر", "cities" => ["الدوحة", "الريان", "الوكره", "الخور", "أم صلال", "الشمال", "مسيعيد", "لوسيل", "دخان"]],
    "OM" => ["name" => "عمان", "cities" => ["مسقط", "صلالة", "صحار", "نزوى", "صور", "البريمي", "الرستاق", "إبراء", "خصب", "عبري", "الدقم", "السيب", "مطرح"]],
    "BH" => ["name" => "البحرين", "cities" => ["المنامة", "المحرق", "الرفاع", "مدينة حمد", "مدينة عيسى", "سترة", "البديع", "جد حفص", "الزلاق"]],
    "JO" => ["name" => "الأردن", "cities" => ["عمان", "الزرقاء", "إربد", "العقبة", "الكرك", "السلط", "مادبا", "جرش", "معان", "الطفيلة", "المفرق", "عجلون", "الرمثا"]],
    "DZ" => ["name" => "الجزائر", "cities" => ["الجزائر العاصمة", "وهران", "قسنطينة", "عنابة", "البليدة", "سطيف", "باتنة", "الجلفة", "سكيكدة", "سيدي بلعباس", "بسكرة", "تلمسان", "بجاية", "ورقلة", "بشار", "تيزي وزو"]],
    "MA" => ["name" => "المغرب", "cities" => ["الرباط", "الدار البيضاء", "فاس", "مراكش", "طنجة", "أغادير", "مكناس", "وجدة", "القنيطرة", "تطوان", "آسفي", "المحمدية", "الجديدة", "الناظور", "العيون", "بني ملال", "الرشيدية", "الصويرة"]],
    "TN" => ["name" => "تونس", "cities" => ["تونس العاصمة", "صفاقس", "سوسة", "القيروان", "بنزرت", "قابس", "أريانة", "المنستير", "قفصة", "نابل", "مدنين", "توزر", "جربة", "تطاوين", "باجة", "القصرين"]],
    "IQ" => ["name" => "العراق", "cities" => ["بغداد", "البصرة", "الموصل", "أربيل", "النجف", "كربلاء", "كركوك", "السليمانية", "الحلة", "الناصرية", "الرمادي", "بعقوبة", "الديوانية", "الكوت", "العمارة", "دهوك", "سامراء", "الفلوجة"]],
    "LB" => ["name" => "لبنان", "cities" => ["بيروت", "طرابلس", "صيدا", "صور", "جونيه", "زحلة", "بعلبك", "جبيل", "النبطية", "البترون", "عاليه", "بشري"]],
    "SY" => ["name" => "سوريا", "cities" => ["دمشق", "حلب", "حمص", "اللاذقية", "حماة", "طرطوس", "الرقة", "دير الزور", "إدلب", "درعا", "الحسكة", "القامشلي", "السويداء"]],
    "PS" => ["name" => "فلسطين", "cities" => ["القدس", "غزة", "رام الله", "نابلس", "الخليل", "بيت لحم", "جنين", "أريحا", "طولكرم", "قلقيلية", "خان يونس", "رفح", "دير البلح", "سلفيت"]],
    "YE" => ["name" => "اليمن", "cities" => ["صنعاء", "عدن", "تعز", "الحديدة", "المكلا", "إب", "ذمار", "صعدة", "عتق", "سيئون", "زنجبار", "البيضاء", "حجة"]],
    "SD" => ["name" => "السودان", "cities" => ["الخرطوم", "أم درمان", "بورتسودان", "كسلا", "الأبيض", "نيالا", "القضارف", "ود مدني", "الفاشر", "عطبرة", "كوستي", "دنقلا"]],
    "LY" => ["name" => "ليبيا", "cities" => ["طرابلس", "بنغازي", "مصراتة", "البيضاء", "طبرق", "الزاوية", "سبها", "سرت", "درنة", "زليتن", "صبراتة", "غريان"]]
];

$HISN_DATA_STATIC = [
    ["ID" => 27, "TITLE" => "أذكار الصباح والمساء", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_028.mp3"],
    ["ID" => 28, "TITLE" => "أذكار النوم", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_029.mp3"],
    ["ID" => 1, "TITLE" => "أذكار الاستيقاظ من النوم", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_002.mp3"],
    ["ID" => 6, "TITLE" => "دعاء دخول الخلاء", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_007.mp3"],
    ["ID" => 7, "TITLE" => "دعاء الخروج من الخلاء", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_008.mp3"],
    ["ID" => 8, "TITLE" => "الذكر قبل الوضوء", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_009.mp3"],
    ["ID" => 9, "TITLE" => "الذكر بعد الفراغ من الوضوء", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_010.mp3"],
    ["ID" => 10, "TITLE" => "الذكر عند الخروج من المنزل", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_011.mp3"],
    ["ID" => 11, "TITLE" => "الذكر عند دخول المنزل", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_012.mp3"],
    ["ID" => 12, "TITLE" => "دعاء الذهاب إلى المسجد", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_013.mp3"],
    ["ID" => 13, "TITLE" => "دعاء دخول المسجد", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_014.mp3"],
    ["ID" => 14, "TITLE" => "دعاء الخروج من المسجد", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_015.mp3"],
    ["ID" => 15, "TITLE" => "أذكار الآذان", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_016.mp3"],
    ["ID" => 16, "TITLE" => "دعاء الاستفتاح", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_017.mp3"],
    ["ID" => 17, "TITLE" => "دعاء الركوع", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_018.mp3"],
    ["ID" => 19, "TITLE" => "دعاء السجود", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_020.mp3"],
    ["ID" => 25, "TITLE" => "الأذكار بعد السلام من الصلاة", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_026.mp3"],
    ["ID" => 34, "TITLE" => "دعاء الهم والحزن", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_035.mp3"],
    ["ID" => 35, "TITLE" => "دعاء الكرب", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_036.mp3"],
    ["ID" => 96, "TITLE" => "دعاء السفر", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_097.mp3"],
    ["ID" => 129, "TITLE" => "الاستغفار والتوبة", "AUDIO_URL" => "https://www.hisnmuslim.com/audio/ar/ar_7esn_AlMoslem_by_Doors_130.mp3"]
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= htmlspecialchars($site_title); ?></title>
    <link rel="icon" type="image/png" href="https://i.ibb.co/js36YFR/file-000000006e8871f89ca2d507729992d5.png">
  
    <!-- الخطوط والأيقونات -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- مكتبة تقليب الصفحات -->
    <script src="https://unpkg.com/page-flip/dist/js/page-flip.browser.js"></script>

    <style>
        :root {
            --bg-body: #050505;
            --bg-sidebar: rgba(0, 0, 0, 0.3);
            --bg-card: rgba(20, 20, 20, 0.6);
            --primary: #5eead4;
            --primary-glow: rgba(94, 234, 212, 0.5);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255, 255, 255, 0.08);
            --sidebar-width: 280px;
            --player-height: 90px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; outline: none; -webkit-tap-highlight-color: transparent; }
        
        body { 
            font-family: 'Cairo', sans-serif; 
            background-color: var(--bg-body); 
            color: var(--text-main); 
            min-height: 100vh; 
            overflow-y: auto;
            display: flex; 
            position: relative;
        }

        .leonardo-bg { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2; background: #000; overflow: hidden; }
        .orb { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.6; animation: floatOrb 20s infinite ease-in-out alternate; }
        .orb-1 { top: -10%; left: -10%; width: 50vw; height: 50vw; background: #4c1d95; animation-duration: 25s; }
        .orb-2 { bottom: -10%; right: -10%; width: 60vw; height: 60vw; background: #0f766e; animation-duration: 30s; animation-direction: reverse; }
        .orb-3 { top: 40%; left: 30%; width: 40vw; height: 40vw; background: #be185d; animation-duration: 28s; opacity: 0.4; }
        .orb-4 { bottom: 20%; left: 10%; width: 30vw; height: 30vw; background: #1d4ed8; animation-duration: 35s; }

        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, 40px) scale(1.1); }
            100% { transform: translate(-20px, -20px) scale(0.95); }
        }

        .grid-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px; pointer-events: none;
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }

        /* Sidebar & Layout */
        .sidebar { 
            width: var(--sidebar-width); 
            position: fixed; top: 0; right: 0; height: 100vh; 
            background: rgba(5, 5, 5, 0.6); backdrop-filter: blur(15px); 
            border-left: 1px solid var(--border); display: flex; flex-direction: column; 
            z-index: 1000; transition: var(--transition); 
        }

        .mobile-menu-btn, .sidebar-close-btn { display: none; }

        @media (min-width: 769px) {
            .main-container { margin-right: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); }
        }

        .brand { padding: 30px; font-size: 1.5rem; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px; text-shadow: 0 0 15px var(--primary-glow); }
        .brand .logo { width: 40px; height: 40px; object-fit: cover; border-radius: 8px; margin-left: 10px; }
        .nav-links { flex: 1; padding: 0 15px; overflow-y: auto; }
        .nav-item { display: flex; align-items: center; gap: 15px; padding: 14px 20px; margin-bottom: 8px; border-radius: 12px; cursor: pointer; color: var(--text-muted); font-weight: 600; transition: var(--transition); }
        .nav-item:hover { background: rgba(255,255,255,0.08); color: white; transform: translateX(-5px); box-shadow: 0 0 10px rgba(94, 234, 212, 0.1); }
        .nav-item.active { background: linear-gradient(90deg, rgba(94, 234, 212, 0.15) 0%, transparent 100%); color: var(--primary); border-right: 3px solid var(--primary); }

        .sidebar-footer { padding: 20px; border-top: 1px solid var(--border); background: rgba(0,0,0,0.3); font-size: 0.85rem; }
        .footer-links { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 10px; justify-content: center; }
        .footer-link { color: var(--text-muted); text-decoration: none; transition: 0.2s; cursor: pointer; font-size: 0.8rem; }
        .footer-link:hover { color: var(--primary); }
        .copyright { color: #64748b; font-size: 0.75rem; margin-top: 10px; display: flex; align-items: center; gap: 5px; justify-content: center; }

        .main-container { flex: 1; position: relative; display: flex; flex-direction: column; min-height: 100vh; }
        .top-bar { padding: 15px 40px; height: 80px; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(to bottom, rgba(0,0,0,0.8), transparent); z-index: 20; }
        .header-left { display: flex; align-items: center; gap: 15px; flex: 1; }
        
        .search-wrapper { position: relative; display: flex; align-items: center; margin-left: auto; }
        .search-toggle-btn { background: rgba(255,255,255,0.05); border: 1px solid var(--border); color: var(--text-muted); width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: var(--transition); z-index: 2; backdrop-filter: blur(5px); }
        .search-input-container { position: absolute; left: 0; top: 0; width: 0; height: 40px; background: rgba(20, 20, 20, 0.9); border-radius: 20px; display: flex; align-items: center; overflow: hidden; transition: width 0.4s; border: 1px solid var(--primary); opacity: 0; backdrop-filter: blur(10px); }
        .search-wrapper.expanded .search-input-container { width: 300px; opacity: 1; padding-left: 40px; }
        .search-input { width: 100%; background: transparent; border: none; color: white; padding: 0 15px; font-family: 'Cairo'; height: 100%; }
        .clear-search-btn { background: none; border: none; color: #ef4444; cursor: pointer; padding: 0 10px; display: none; }

        .page-title h2 { font-size: 1.3rem; margin-bottom: 2px; color: var(--text-main); text-shadow: 0 2px 10px rgba(0,0,0,0.5); }
        .page-title p { font-size: 0.85rem; color: var(--text-muted); font-weight: 400; margin-top: 0; }

        /* Views & Cards */
        .content-view { flex: 1; padding: 0 40px 120px 40px; overflow-y: visible; display: none; scroll-behavior: smooth; }
        .content-view.active { display: block; animation: fadeIn 0.4s ease-out; }

        .reciter-info-header { background: linear-gradient(90deg, rgba(94, 234, 212, 0.1), transparent); border-right: 3px solid var(--primary); padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; color: var(--primary); font-weight: 700; backdrop-filter: blur(5px); }
        .grid-layout { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }

        .card { 
            background-image: linear-gradient(rgba(30, 41, 59, 0.85), rgba(15, 23, 42, 0.95)), url('https://www.transparenttextures.com/patterns/arabesque.png');
            background-size: 300px; background-blend-mode: overlay; backdrop-filter: blur(8px);
            border-radius: 16px; padding: 20px; border: 1px solid var(--border); cursor: pointer; 
            transition: var(--transition); position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            opacity: 0; transform: translateY(30px);
            transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: opacity, transform;
        }
        .card.reveal-active { opacity: 1; transform: translateY(0); }
        .card:hover { border-color: var(--primary); box-shadow: 0 10px 25px rgba(94, 234, 212, 0.15); background-color: rgba(30, 41, 59, 0.7); }
        .card h3 { font-size: 1.1rem; margin-bottom: 5px; color: var(--text-main); font-weight: 700; }
        .card p { font-size: 0.85rem; color: var(--text-muted); }

        .hadith-text { 
            font-family: 'Cairo', sans-serif; font-size: 1.5rem; line-height: 1.8; font-weight: 700; 
            color: #ffffff; text-align: center; padding: 20px 10px; text-shadow: 0 2px 8px rgba(0,0,0,0.6); 
        }

        .card.active-track { border-color: var(--primary); background-image: linear-gradient(145deg, rgba(94, 234, 212, 0.15) 0%, rgba(15, 23, 42, 0.9) 100%); transform: scale(0.98); }
        .card.active-track h3 { color: var(--primary); }
        
        .playing-indicator { position: absolute; top: 15px; left: 15px; display: flex; align-items: center; justify-content: center; height: 20px; width: 20px; }
        .playing-indicator .fa-play { color: var(--primary); font-size: 1rem; display: block; filter: drop-shadow(0 0 5px var(--primary-glow)); }
        .playing-indicator .eq-bars { display: none; gap: 3px; height: 15px; align-items: flex-end; }
        .playing-bar { width: 3px; background: var(--primary); animation: equalizer 1s infinite ease-in-out alternate; box-shadow: 0 0 5px var(--primary-glow); }
        .playing-bar:nth-child(1) { height: 60%; animation-delay: 0s; }
        .playing-bar:nth-child(2) { height: 100%; animation-delay: 0.2s; }
        .playing-bar:nth-child(3) { height: 40%; animation-delay: 0.4s; }
        @keyframes equalizer { 0% { height: 30%; } 100% { height: 100%; } }
        .card.active-track.playing-state .playing-indicator .fa-play { display: none; }
        .card.active-track.playing-state .playing-indicator .eq-bars { display: flex; }

        .back-to-top { position: fixed; left: 22.5px; bottom: 85px; width: 45px; height: 45px; background: rgba(20, 20, 20, 0.9); backdrop-filter: blur(10px); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; opacity: 0; pointer-events: none; transition: all 0.4s; z-index: 90; box-shadow: 0 5px 15px rgba(0,0,0,0.3); border: 1px solid var(--border); }
        .back-to-top:hover { background: var(--primary); border-color: var(--primary); color: #000; }  
        .back-to-top.show { opacity: 1; pointer-events: all; }
        body.player-active .back-to-top { bottom: 180px; }

        /* Player Dock */
        .player-dock { 
            background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);
            position: fixed; bottom: 10px; left: 10px; right: calc(var(--sidebar-width) + 10px); 
            border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); 
            display: flex; align-items: center; box-sizing: border-box; 
            padding: 15px 25px; z-index: 100; transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55); box-shadow: 0 10px 30px rgba(0,0,0,0.5); 
        }
        .player-inner-container { display: flex; flex-direction: column; width: 100%; align-items: center; gap: 10px; }
        .player-dock.minimized { transform: translateY(150%); pointer-events: none; }
        .player-dock, .restore-player-btn { display: none !important; }
        body.player-active .player-dock { display: flex !important; }
        body.player-active .player-dock.minimized ~ .restore-player-btn { display: flex !important; transform: scale(1); }

        .restore-player-btn {
            position: fixed; bottom: 20px; left: 20px; width: 50px; height: 50px; border-radius: 50%;
            background: rgba(15, 15, 15, 0.95); color: var(--primary); backdrop-filter: blur(10px);
            display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 110;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3); border: 1px solid var(--border);
            transform: scale(0); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .restore-player-btn.playing i { display: none !important; }
        .restore-player-btn.playing .mini-eq-container { display: flex !important; }
        .mini-eq-container { display: none; align-items: flex-end; gap: 3px; height: 18px; }
        .mini-bar { width: 4px; background: var(--primary); border-radius: 2px; animation: mini-equalizer 0.8s infinite ease-in-out alternate; }
        @keyframes mini-equalizer { 0% { height: 20%; } 100% { height: 100%; } }

        .minimize-btn { position: absolute; top: -15px; right: 15px; background: var(--bg-card); color: var(--primary); width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border); cursor: pointer; font-size: 0.8rem; }
        .controls-area { width: 100%; display: flex; flex-direction: column; align-items: center; gap: 8px; }
        .btn-ctrl { background: none; border: none; color: var(--text-muted); font-size: 1.1rem; cursor: pointer; transition: 0.2s; }
        .btn-ctrl:hover { color: white; }
        .btn-play { width: 42px; height: 42px; background: var(--primary); color: #000; border-radius: 50%; border:none; cursor: pointer; display:flex; align-items:center; justify-content:center; box-shadow: 0 0 20px var(--primary-glow); }
        .progress-wrapper { width: 100%; max-width: 600px; display: flex; align-items: center; gap: 10px; font-size: 0.7rem; color: var(--text-muted); direction: ltr; }
        .progress-bar-bg { flex: 1; height: 4px; background: rgba(255,255,255,0.1); border-radius: 4px; cursor: pointer; position: relative; }
        .progress-fill { height: 100%; background: var(--primary); border-radius: 4px; width: 0%; box-shadow: 0 0 10px var(--primary-glow); }
        .track-info { width: 80%; display: flex; flex-direction: row; justify-content: center; margin-bottom: 8px; gap: 5px 10px; }
        .track-title { font-weight: 800; font-size: 0.95rem; white-space: nowrap; overflow: hidden; color: var(--text-main); }
        .track-artist { font-size: 0.90rem; color: var(--primary); white-space: nowrap; overflow: hidden; }
        .info-separator { color: var(--border); font-size: 1rem; }

        .main-controls { display: flex; justify-content: space-between; align-items: center; width: 100%; max-width: 800px; margin: 0 auto; }
        .ctrl-group { display: flex; align-items: center; flex: 1; }
        .group-left { justify-content: flex-start; gap: 15px; }
        .group-center { justify-content: center; gap: 25px; flex: 0 0 auto; min-width: 180px; }
        .group-right { justify-content: flex-end; gap: 15px; }
        .volume-area { display: flex; align-items: center; gap: 5px; min-width: 135px; justify-content: flex-end; direction:ltr; }

        .volume-slider {
            width: 50px !important; -webkit-appearance: none; height: 5px; background: rgba(255, 255, 255, 0.1);
            border-radius: 5px; outline: none; cursor: pointer;
            background-image: linear-gradient(var(--primary), var(--primary));
            background-size: 100% 100%; background-repeat: no-repeat;
        }
        .volume-slider::-webkit-slider-thumb { -webkit-appearance: none; width: 10px; height: 10px; border-radius: 50%; background: white; border: 1.5px solid var(--primary); cursor: pointer; }

        /* Mushaf Overlay */
        .mushaf-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: #050505; z-index: 2000; display: none; flex-direction: column; font-family: 'Cairo', sans-serif; animation: fadeIn 0.3s ease; }
        .mushaf-overlay.active { display: flex; }
        .mushaf-navbar { height: 60px; width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; background: rgba(20, 20, 20, 0.95); backdrop-filter: blur(15px); border-bottom: 1px solid var(--border); z-index: 1000; position: absolute; top: 0; left: 0; right: 0; transition: transform 0.3s ease; }
        .mushaf-navbar.hidden-bar { transform: translateY(-100%); }
        .mushaf-toggle-center { position: absolute; left: 50%; transform: translateX(-50%); width: 70px; height: 28px; background: rgba(20, 20, 20, 0.95); border: 1px solid var(--border); color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 1001; border-radius: 0 0 15px 15px; transition: top 0.3s ease, background 0.3s; font-size: 0.9rem; }
        .mushaf-toggle-center.menu-visible { top: 60px; border-top: none; }
        .mushaf-toggle-center.menu-hidden { top: 0; background: rgba(20, 20, 20, 0.7); }
        .book-container { flex: 1; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; position: relative; padding-top: 60px; transition: padding 0.3s; overflow: hidden; }
        #book { transform: scaleX(-1); z-index: 1; }
        .page-content { background-color: #fffdf5; display: flex; justify-content: center; align-items: center; width: 100%; height: 100%; overflow: hidden; transform: scaleX(-1); padding: 20px 25px; box-sizing: border-box; }
        .page-content img { width: 100%; height: 100%; object-fit: contain; display: block; opacity: 0; transition: opacity 0.4s ease-in; position: relative; z-index: 1; }
        .click-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; display: flex; }
        .click-zone { width: 50%; height: 100%; cursor: pointer; }
        .mushaf-select { background-color: var(--bg-card); color: var(--text-main); border: 1px solid var(--border); padding: 0 10px; height: 35px; border-radius: 8px; font-size: 0.85rem; outline: none; cursor: pointer; }
        .mushaf-btn { background: var(--bg-card); border: 1px solid var(--border); color: var(--text-main); height: 35px; width: 35px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .mushaf-btn.danger { color: #ef4444; border-color: rgba(239, 68, 68, 0.3); }

        /* Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px); z-index: 3000; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; }
        .modal-overlay.open { display: flex; opacity: 1; }
        .modal-card { background: rgba(30, 41, 59, 0.95); border: 1px solid var(--primary); box-shadow: 0 0 30px rgba(94, 234, 212, 0.2); border-radius: 20px; width: 90%; max-width: 600px; padding: 30px; transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); max-height: 85vh; overflow-y: auto; }
        .modal-overlay.open .modal-card { transform: scale(1); }
        .modal-header { font-size: 1.5rem; font-weight: 700; color: var(--primary); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 15px; }
        .modal-close { cursor: pointer; color: var(--text-muted); transition: 0.2s; }
        .modal-close:hover { color: #ef4444; }
        .modal-body { font-size: 0.95rem; line-height: 1.8; color: var(--text-muted); text-align: justify; }

        /* Mobile */
        @media (max-width: 768px) {
            .mobile-menu-btn { display: flex !important; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.05); border-radius: 8px; color: var(--primary); margin-left: 10px; cursor: pointer; border: 1px solid var(--border); }
            .sidebar-close-btn { display: block !important; cursor: pointer; font-size: 1.5rem; color: var(--text-muted); padding: 5px; margin-right: auto; }
            .sidebar { position: fixed; right: -100%; top: 0; width: 100%; height: 100vh; z-index: 9999; transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1); background: rgba(5, 5, 5, 0.98); backdrop-filter: blur(20px); border-left: none; }
            .sidebar.active { right: 0; }
            .player-dock { left: 10px; right: 10px; bottom: 10px; padding: 12px 15px; min-height: 140px; }
            .main-container { margin-right: 0; width: 100%; }
            .content-view { padding: 10px 10px 120px 10px; }
            .volume-slider, #volPercent { display: none !important; }
        }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* General Toast Status */
        #mushaf-status {
            position: fixed; top: 30px; left: 50%; transform: translateX(-50%) translateY(-20px);
            background: rgba(30, 41, 59, 0.85) !important; backdrop-filter: blur(12px) !important;
            color: #ffffff !important; padding: 12px 35px; border-radius: 20px; z-index: 100000;
            font-weight: 700; text-align: center; border: 1px solid var(--primary);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4); display: none; opacity: 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        #mushaf-status.show { display: block; opacity: 1; transform: translateX(-50%) translateY(0); }
        
        body.radio-active #downloadBtn, body.radio-active #playerMushafBtn { display: none !important; }
        body.azkar-active #playerMushafBtn, body.azkar-active #playerFavBtn { display: none !important; }
    </style>
</head>
<body>

    <div id="mushaf-status"></div>
  
    <div class="leonardo-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="orb orb-4"></div>
    </div>
    <div class="grid-overlay"></div>

    <!-- القائمة الجانبية المحدثة بـ PHP -->
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <img class="logo" src="https://i.ibb.co/js36YFR/file-000000006e8871f89ca2d507729992d5.png" alt="Logo">
            <span><?= htmlspecialchars($site_title); ?></span>
            <i class="fas fa-times sidebar-close-btn" onclick="toggleSidebar()"></i>
        </div>
        <nav class="nav-links">
            <div class="nav-item" onclick="navigate('favorites')"><i class="fas fa-heart"></i> المفضلة</div>
            <div class="nav-item active" onclick="navigate('quran')"><i class="fas fa-headphones-alt"></i> القرآن الكريم </div>
            <div class="nav-item" onclick="navigate('radio')"><i class="fas fa-radio"></i> الإذاعات</div> 
            <div class="nav-item" onclick="openMushafOverlay()"><i class="fas fa-book-open"></i> المصحف الشريف</div>
            <div class="nav-item" onclick="navigate('hadith')"><i class="fas fa-book-reader"></i> الحديث الشريف</div>
            <div class="nav-item" onclick="navigate('azkar')"><i class="fas fa-shield-halved"></i> حصن المسلم</div>
            <div class="nav-item" onclick="navigate('prayer')"><i class="fas fa-mosque"></i> مواقيت الصلاة</div>
            <div class="nav-item" onclick="navigate('asma')"><i class="fas fa-star-and-crescent"></i> أسماء الله الحسنى</div>
        </nav>
        
        <div class="sidebar-footer">
            <div class="footer-links">
                <a class="footer-link" onclick="openModal('privacy')">سياسة الخصوصية</a>
                <span style="color:var(--border)">|</span>
                <a class="footer-link" onclick="openModal('terms')">شروط الاستخدام</a>
                <span style="color:var(--border)">|</span>
                <a class="footer-link" onclick="openModal('contact')">اتصل بنا</a>
            </div>
            <div class="copyright">
                <i class="far fa-copyright"></i> 
                <span><?= $current_year; ?></span> QuranMp3 - خادم PHP
            </div>
        </div>
    </aside>

    <div class="main-container">
        <div class="top-bar">
            <div class="header-left">
                <div class="mobile-menu-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>
                <div class="page-title" id="pageHeader">
                    <h2>القرآن الكريم</h2>
                    <p>استمع للقرآن الكريم</p>
                </div>
            </div>
            
            <button class="fav-header-btn" onclick="navigate('favorites')" title="المفضلة" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border); color: var(--text-muted); width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; margin-left: 10px;">
                <i class="fas fa-heart"></i>
            </button>
            
            <div class="search-wrapper" id="searchWrapper">
                <div class="search-input-container">
                    <input type="text" class="search-input" id="searchInput" placeholder="ابحث هنا..." oninput="handleSearchInput()">
                    <button class="clear-search-btn" id="clearSearchBtn" onclick="clearSearch()"><i class="fas fa-trash-can"></i></button>
                </div>
                <button class="search-toggle-btn" onclick="toggleSearch()"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <!-- واجهة القرآن -->
        <div id="quranView" class="content-view active" onscroll="checkScroll(this)">
            <div id="quranBreadcrumb" style="display: none;">
                <div class="reciter-info-header">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-user-circle fa-lg"></i>
                        <span id="selectedReciterName">اسم القارئ</span>
                        <span style="font-size: 0.8rem; opacity: 0.7; margin-right: 5px;">(الرواية: <span id="selectedRewaya">--</span>)</span>
                    </div>
                    <button onclick="resetQuranView()" style="background:none; border:1px solid var(--primary); color:var(--primary); padding:5px 15px; border-radius:20px; cursor:pointer; font-size:0.85rem;">
                        <i class="fas fa-arrow-right"></i> تغيير القارئ
                    </button>
                </div>
            </div>
            <div id="quranGrid" class="grid-layout">
                <div style="text-align: center; grid-column: 1/-1; margin-top: 50px;"><i class="fas fa-circle-notch fa-spin fa-2x" style="color: var(--primary);"></i></div>
            </div>
        </div>

        <!-- واجهة الحديث النبوي مولدة بالـ PHP -->
        <div id="hadithView" class="content-view" onscroll="checkScroll(this)">
            <div id="hadithBreadcrumb" style="margin-bottom: 20px; display: none;">
                <button onclick="resetHadithView()" style="background:none; border:1px solid var(--border); color:var(--primary); padding:5px 15px; border-radius:20px; cursor:pointer;"><i class="fas fa-arrow-right"></i> الكتب</button>
            </div>
            <div id="hadithGrid" class="grid-layout">
                <?php foreach ($HADITH_BOOKS as $book): ?>
                    <div class="card" onclick="openHadithBook('<?= $book['id']; ?>')">
                        <h3><?= htmlspecialchars($book['name']); ?></h3>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="hadithLoader" style="text-align: center; padding: 20px; display: none;"><i class="fas fa-circle-notch fa-spin" style="color: var(--primary);"></i> ...جاري التحميل</div>
        </div>
      
        <!-- واجهة المفضلة -->
        <div id="favoritesView" class="content-view" onscroll="checkScroll(this)">
            <div class="fav-tabs-container" style="display:flex; gap:10px; margin-bottom:25px; flex-wrap:wrap; border-bottom:1px solid var(--border); padding-bottom:15px;">
                <div class="fav-tab active" onclick="switchFavTab('quran')" style="padding:8px 18px; border-radius:20px; background:var(--primary); color:#000; cursor:pointer; font-weight:700;">القراء</div>
                <div class="fav-tab" onclick="switchFavTab('surahs')" style="padding:8px 18px; border-radius:20px; background:rgba(255,255,255,0.03); border:1px solid var(--border); cursor:pointer;">السور</div>
                <div class="fav-tab" onclick="switchFavTab('radio')" style="padding:8px 18px; border-radius:20px; background:rgba(255,255,255,0.03); border:1px solid var(--border); cursor:pointer;">الإذاعات</div>
                <div class="fav-tab" onclick="switchFavTab('hadith')" style="padding:8px 18px; border-radius:20px; background:rgba(255,255,255,0.03); border:1px solid var(--border); cursor:pointer;">الأحاديث</div>
            </div>
            <div id="favoritesGrid" class="grid-layout"></div>
        </div>
        
        <!-- واجهة الإذاعات -->
        <div id="radioView" class="content-view" onscroll="checkScroll(this)">
            <div id="radioGrid" class="grid-layout">
                <div style="text-align: center; grid-column: 1/-1; margin-top: 50px;"><i class="fas fa-circle-notch fa-spin fa-2x" style="color: var(--primary);"></i></div>
            </div>
        </div>

        <!-- واجهة مواقيت الصلاة (القوائم مولدة عبر PHP) -->
        <div id="prayerView" class="content-view" onscroll="checkScroll(this)">
            <div style="background: linear-gradient(90deg, rgba(94, 234, 212, 0.05), transparent); border-right: 3px solid var(--primary); padding: 20px; border-radius: 12px; margin-bottom: 30px;">
                <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                    <select id="countrySelect" class="mushaf-select" onchange="loadCities()" style="min-width: 200px; height: 45px;">
                        <option value="" disabled selected>اختر الدولة</option>
                        <?php foreach ($ARAB_COUNTRIES_DATA as $code => $country): ?>
                            <option value="<?= $code; ?>"><?= htmlspecialchars($country['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="citySelect" class="mushaf-select" onchange="getPrayerTimes(true)" style="min-width: 200px; height: 45px;">
                        <option value="" disabled selected>اختر المدينة</option>
                    </select>
                </div>
            </div>

            <div style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.95)); border: 1px solid var(--border); border-radius: 20px; padding: 30px; text-align: center; max-width: 800px; margin: 0 auto 30px auto;">
                <span id="gregorianDate">--</span> | <span id="hijriDate">--</span>
                <div style="margin: 15px 0;">
                    <p>المتبقي على صلاة <span id="nextPrayerName" style="color:var(--primary)">--</span></p>
                    <h1 id="countdownTimer" style="font-size: 3rem; color: #fff; direction: ltr;">00:00:00</h1>
                </div>
                <div style="color: var(--text-muted);"><i class="fas fa-map-marker-alt"></i> <span id="locationDisplay">--</span></div>
            </div>

            <div id="prayerGrid" class="grid-layout" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));"></div>
        </div>
      
        <!-- أسماء الله الحسنى -->
        <div id="asmaView" class="content-view" onscroll="checkScroll(this)">
            <div id="asmaGrid" class="grid-layout">
                <div style="text-align: center; grid-column: 1/-1; margin-top: 50px;"><i class="fas fa-circle-notch fa-spin fa-2x" style="color: var(--primary);"></i></div>
            </div>
        </div>
      
        <!-- حصن المسلم -->
        <div id="azkarView" class="content-view" onscroll="checkScroll(this)">
            <div id="azkarGrid" class="grid-layout">
                <div style="text-align: center; grid-column: 1/-1; margin-top: 50px;"><i class="fas fa-circle-notch fa-spin fa-2x" style="color: var(--primary);"></i></div>
            </div>
        </div>

        <div class="back-to-top" id="backToTopBtn" onclick="scrollToTop()">
            <i class="fas fa-arrow-up"></i>
        </div>
    </div>

    <!-- مشغل الصوت السفلي -->
    <div class="player-dock" id="playerDock">
        <div class="minimize-btn" onclick="minimizePlayer()"><i class="fas fa-chevron-down"></i></div>
        <div class="player-inner-container">
            <div class="track-info">
                <div class="track-title" id="pTitle">--</div>
                <span class="info-separator">•</span>
                <div class="track-artist" id="pArtist">--</div>
            </div>
            <div class="controls-area">
                <div class="main-controls">
                    <div class="ctrl-group group-left">
                        <button class="btn-ctrl" id="playerFavBtn" onclick="toggleCurrentTrackFav()"><i class="far fa-heart"></i></button>
                        <button class="btn-ctrl" id="playerMushafBtn" onclick="openSyncMushaf()"><i class="fas fa-book-open"></i></button>
                    </div>
                    <div class="ctrl-group group-center">
                        <button class="btn-ctrl" onclick="nextTrack()"><i class="fas fa-step-forward"></i></button>
                        <button class="btn-play" onclick="togglePlayState()" id="playBtn"><i class="fas fa-play"></i></button>
                        <button class="btn-ctrl" onclick="prevTrack()"><i class="fas fa-step-backward"></i></button>
                    </div>
                    <div class="ctrl-group group-right">
                        <button class="btn-ctrl" id="downloadBtn" onclick="downloadCurrentTrack()"><i class="fas fa-download"></i></button>
                        <div class="volume-area">
                            <button class="btn-ctrl" onclick="toggleMute()" id="muteBtn"><i class="fas fa-volume-up"></i></button>
                            <input type="range" class="volume-slider" id="volSlider" min="0" max="1" step="0.01" value="1" oninput="setVolume(this.value)">
                            <span id="volPercent" style="font-size: 0.7rem; color: var(--text-muted); width: 35px;">100%</span>
                        </div>
                    </div>
                </div>
                <div class="progress-wrapper" id="progressWrapper">             
                    <span id="currTime">00:00</span>
                    <div class="progress-bar-bg" id="progressBar" onclick="seekAudio(event)">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <span id="durTime">00:00</span>
                </div>
            </div>
        </div>
    </div>
  
    <div class="restore-player-btn" onclick="restorePlayer()">
        <i class="fa-solid fa-play"></i>
        <div class="mini-eq-container">
            <div class="mini-bar"></div><div class="mini-bar"></div><div class="mini-bar"></div>
        </div>
    </div>

    <audio id="audioElement"></audio>

    <!-- النوافذ المنبثقة -->
    <div id="infoModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <span id="modalTitle">العنوان</span>
                <i class="fas fa-times modal-close" onclick="closeModal()"></i>
            </div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>

    <!-- عارض المصحف الشريف المطور -->
    <div id="mushafOverlay" class="mushaf-overlay">
        <div id="mushafLoader" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: none; flex-direction: column; align-items: center; gap: 15px; color: var(--primary); z-index: 2002;">
            <i class="fas fa-circle-notch fa-spin fa-3x"></i>
            <p>جاري تحميل المصحف الشريف...</p>
        </div>
        <div id="mushafToggleBtn" class="mushaf-toggle-center menu-visible" onclick="toggleMushafNavbar()">
            <i class="fas fa-chevron-up" id="mushafToggleIcon"></i>
        </div>
        <div class="mushaf-navbar" id="mushafNavbar">
            <div style="display:flex; align-items:center; gap:10px;">
                <button class="mushaf-btn danger" onclick="closeMushafOverlay()"><i class="fas fa-times"></i></button>
                <div style="display:flex; gap:5px;">
                    <select id="surahSelect" class="mushaf-select"><option value="" disabled selected>السورة</option></select>
                    <select id="juzSelect" class="mushaf-select"><option value="" disabled selected>الجزء</option></select>
                    <select id="hizbSelect" class="mushaf-select"><option value="" disabled selected>الحزب</option></select>
                </div>
            </div>
            <button class="mushaf-btn" id="mushafFullscreenBtn"><i class="fas fa-expand"></i></button>
        </div>
        <div class="book-container">
            <div class="click-overlay">
                <div class="click-zone right" id="rightZone" title="السابق"></div>
                <div class="click-zone left" id="leftZone" title="التالي"></div>
            </div>
            <div id="book"></div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- تغذية جافاسكربت ببيانات PHP المشفرة واستمرار تشغيل الصوت -->
    <!-- ======================================================== -->
    <script>
        // تمرير البيانات من PHP إلى JS مباشرة
        const SURAH_NAMES = <?= json_encode($SURAH_NAMES, JSON_UNESCAPED_UNICODE); ?>;
        const ARAB_COUNTRIES_DATA = <?= json_encode($ARAB_COUNTRIES_DATA, JSON_UNESCAPED_UNICODE); ?>;
        const HISN_DATA_STATIC = <?= json_encode($HISN_DATA_STATIC, JSON_UNESCAPED_UNICODE); ?>;

        const API_RECITERS = 'index.php?api_proxy=' + encodeURIComponent('https://www.mp3quran.net/api/v3/reciters?language=ar');
        const API_RADIOS = 'index.php?api_proxy=' + encodeURIComponent('https://mp3quran.net/api/v3/radios?language=ar');
        const MUSHAF_PAGES_API = 'index.php?api_proxy=' + encodeURIComponent('https://quran.yousefheiba.com/api/quranPagesImage');

        const PRAYER_NAMES_AR = { "Fajr": "الفجر", "Sunrise": "الشروق", "Dhuhr": "الظهر", "Asr": "العصر", "Maghrib": "المغرب", "Isha": "العشاء" };

        const surahsData = SURAH_NAMES.map((name, index) => {
            const starts = [1,2,50,77,106,128,151,177,187,208,221,235,249,255,262,267,282,293,305,312,322,332,342,350,359,367,377,385,396,404,411,415,418,428,434,440,446,453,458,467,477,483,489,496,499,502,507,511,515,518,520,523,526,528,531,534,537,542,545,549,551,553,554,556,558,560,562,564,566,568,570,572,574,575,577,578,580,582,583,585,586,587,587,589,590,591,591,592,593,594,595,595,596,596,597,597,598,598,599,599,600,600,601,601,601,602,602,602,603,603,603,604,604,604];
            return { n: name, p: starts[index] };
        });
        const juzStartPages = [1, 22, 42, 62, 82, 102, 122, 142, 162, 182, 202, 222, 242, 262, 282, 302, 322, 342, 362, 382, 402, 422, 442, 462, 482, 502, 522, 542, 562, 582];

        let state = {
            reciters: [], currentReciter: null, playlist: [], currentIndex: -1, 
            radios: [], isRadioMode: false, isAzkarMode: false, isPlaying: false,
            currentView: 'quran', hadithBookId: null, hadithTotalLoaded: 0,
            pageFlipInstance: null, mushafLoaded: false
        };

        const audio = document.getElementById('audioElement');
        let prayerInterval;

        let favorites = {
            reciters: JSON.parse(localStorage.getItem('fav_reciters')) || [],
            radios: JSON.parse(localStorage.getItem('fav_radios')) || [],
            hadiths: JSON.parse(localStorage.getItem('fav_hadiths')) || [],
            surahs: JSON.parse(localStorage.getItem('fav_surahs')) || [] 
        };
        let currentFavTab = 'quran';

        window.onload = () => {
            initReciters();
            initRadios();
            initMushafControls();
            initPrayerTimes();
            applyScrollReveal();
        };

        // Navigation
        function navigate(view) {
            document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
            document.querySelectorAll('.content-view').forEach(v => v.classList.remove('active'));
            document.getElementById('mushafOverlay').classList.remove('active');

            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth <= 768 && sidebar.classList.contains('active')) toggleSidebar();

            const target = document.getElementById(view + 'View');
            if (target) target.classList.add('active');

            const searchWrapper = document.getElementById('searchWrapper');
            searchWrapper.style.display = ['prayer', 'asma', 'favorites'].includes(view) ? 'none' : 'flex';

            const header = document.getElementById('pageHeader');
            if (view === 'quran') { header.innerHTML = '<h2>القرآن الكريم</h2><p>استمع لأعذب التلاوات</p>'; resetQuranView(); }
            else if (view === 'hadith') { header.innerHTML = '<h2>الحديث الشريف</h2><p>كتب السنة النبوية</p>'; resetHadithView(); }
            else if (view === 'radio') { header.innerHTML = '<h2>إذاعات القرآن</h2><p>بث مباشر</p>'; }
            else if (view === 'prayer') { header.innerHTML = '<h2>مواقيت الصلاة</h2><p>كتاباً موقوتاً</p>'; }
            else if (view === 'asma') { header.innerHTML = '<h2>أسماء الله الحسنى</h2><p>وَلِلَّهِ الْأَسْمَاءُ الْحُسْنَىٰ فَادْعُوهُ بِهَا</p>'; initAsma(); }
            else if (view === 'favorites') { header.innerHTML = '<h2>المفضلة</h2><p>ما تم حفظه للوصول السريع</p>'; renderFavoritesGrid(); }
            else if (view === 'azkar') { header.innerHTML = '<h2>حصن المسلم</h2><p>أذكار الكتاب والسنة</p>'; initAzkar(); }

            state.currentView = view;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        // Quran logic
        async function initReciters() {
            try {
                const res = await fetch(API_RECITERS);
                const data = await res.json();
                state.reciters = data.reciters.sort((a, b) => a.name.localeCompare(b.name, 'ar'));
                renderReciters(state.reciters);
            } catch(e) { console.error(e); }
        }

        function renderReciters(list) {
            const grid = document.getElementById('quranGrid');
            grid.innerHTML = '';
            list.forEach(r => {
                const d = document.createElement('div');
                d.className = 'card';
                const isF = isFav('reciters', r.id) ? 'active' : '';
                d.innerHTML = `<div class="fav-btn-card ${isF}" onclick='toggleFav(event, "reciters", ${JSON.stringify(r)})'><i class="fas fa-heart"></i></div><h3>${r.name}</h3><p>${r.moshaf[0].name}</p>`;
                d.onclick = () => openReciter(r);
                grid.appendChild(d);
            });
            applyScrollReveal();
        }

        function openReciter(r) {
            state.currentReciter = r;
            state.currentView = 'surahs';
            document.getElementById('quranBreadcrumb').style.display = 'block';
            document.getElementById('selectedReciterName').textContent = r.name;
            document.getElementById('selectedRewaya').textContent = r.moshaf[0].name;

            state.playlist = r.moshaf[0].surah_list.split(',').map(Number);
            const grid = document.getElementById('quranGrid');
            grid.innerHTML = '';
            state.playlist.forEach((id, idx) => {
                const d = document.createElement('div');
                d.className = 'card';
                d.innerHTML = `<h3>${String(id).padStart(2,'0')}. ${SURAH_NAMES[id-1]}</h3>`;
                d.onclick = () => playTrack(idx);
                grid.appendChild(d);
            });
            applyScrollReveal();
            scrollToTop();
        }

        function resetQuranView() {
            state.currentView = 'quran';
            document.getElementById('quranBreadcrumb').style.display = 'none';
            renderReciters(state.reciters);
        }

        function playTrack(idx) {
            activatePlayerUI();
            state.isRadioMode = false;
            state.isAzkarMode = false;
            document.body.classList.remove('radio-active', 'azkar-active');

            state.currentIndex = idx;
            const id = state.playlist[idx];
            document.getElementById('pTitle').textContent = `سورة: ${SURAH_NAMES[id - 1]}`;
            document.getElementById('pArtist').textContent = state.currentReciter.name;

            audio.src = `${state.currentReciter.moshaf[0].server}${String(id).padStart(3,'0')}.mp3`;
            audio.play().then(() => {
                state.isPlaying = true;
                updatePlayerUI();
            });
            restorePlayer();
        }

        // Radio logic
        async function initRadios() {
            try {
                const res = await fetch(API_RADIOS);
                const data = await res.json();
                state.radios = data.radios.sort((a,b) => a.name.localeCompare(b.name, 'ar'));
                const grid = document.getElementById('radioGrid');
                grid.innerHTML = '';
                state.radios.forEach((r, idx) => {
                    const d = document.createElement('div');
                    d.className = 'card';
                    d.innerHTML = `<h3>${r.name}</h3><p>بث مباشر</p>`;
                    d.onclick = () => playRadio(idx);
                    grid.appendChild(d);
                });
                applyScrollReveal();
            } catch(e) { console.error(e); }
        }

        function playRadio(idx) {
            activatePlayerUI();
            state.isRadioMode = true;
            state.isAzkarMode = false;
            document.body.classList.add('radio-active');
            document.body.classList.remove('azkar-active');

            state.currentIndex = idx;
            const r = state.radios[idx];
            document.getElementById('pTitle').textContent = r.name;
            document.getElementById('pArtist').textContent = "إذاعة مباشرة";

            audio.src = r.url;
            audio.play().then(() => {
                state.isPlaying = true;
                updatePlayerUI();
            });
            restorePlayer();
        }

        // Hadith logic
        function openHadithBook(id) {
            state.hadithBookId = id;
            state.hadithTotalLoaded = 0;
            document.getElementById('hadithBreadcrumb').style.display = 'block';
            document.getElementById('hadithGrid').innerHTML = '';
            loadMoreHadiths();
        }

        async function loadMoreHadiths() {
            const start = state.hadithTotalLoaded + 1;
            const end = start + 19;
            const url = `index.php?api_proxy=` + encodeURIComponent(`https://api.hadith.gading.dev/books/${state.hadithBookId}?range=${start}-${end}`);
            try {
                const res = await fetch(url);
                const data = await res.json();
                data.data.hadiths.forEach(h => {
                    const d = document.createElement('div');
                    d.className = 'card';
                    d.innerHTML = `<div style="color:var(--primary); margin-bottom:10px;">حديث رقم ${h.number}</div><div class="hadith-text">${h.arab}</div>`;
                    document.getElementById('hadithGrid').appendChild(d);
                });
                state.hadithTotalLoaded += data.data.hadiths.length;
                applyScrollReveal();
            } catch(e) { console.error(e); }
        }

        function resetHadithView() {
            document.getElementById('hadithBreadcrumb').style.display = 'none';
            location.reload(); // أو إعادة بناء البطاقات
        }

        // Azkar Logic
        function initAzkar() {
            const grid = document.getElementById('azkarGrid');
            grid.innerHTML = '';
            HISN_DATA_STATIC.forEach((item, idx) => {
                const card = document.createElement('div');
                card.className = 'card';
                card.innerHTML = `<h3>${item.TITLE}</h3><p>أذكار صوتية</p>`;
                card.onclick = () => {
                    activatePlayerUI();
                    state.isRadioMode = false;
                    state.isAzkarMode = true;
                    state.currentIndex = idx;
                    document.body.classList.add('azkar-active');
                    document.body.classList.remove('radio-active');
                    document.getElementById('pTitle').textContent = item.TITLE;
                    document.getElementById('pArtist').textContent = "حصن المسلم";
                    audio.src = item.AUDIO_URL;
                    audio.play();
                    restorePlayer();
                };
                grid.appendChild(card);
            });
            applyScrollReveal();
        }

        // Prayer Logic
        function initPrayerTimes() {
            const savedCountry = localStorage.getItem('atheer_country') || 'SA';
            const savedCity = localStorage.getItem('atheer_city') || 'مكة المكرمة';
            document.getElementById('countrySelect').value = savedCountry;
            loadCities(savedCity);
            getPrayerTimes(false);
        }

        function loadCities(defaultCity = null) {
            const code = document.getElementById('countrySelect').value;
            const citySel = document.getElementById('citySelect');
            citySel.innerHTML = '<option value="" disabled selected>اختر المدينة</option>';
            if (ARAB_COUNTRIES_DATA[code]) {
                ARAB_COUNTRIES_DATA[code].cities.forEach(city => {
                    const opt = document.createElement('option');
                    opt.value = city; opt.innerText = city;
                    citySel.appendChild(opt);
                });
                if(defaultCity) citySel.value = defaultCity;
            }
        }

        async function getPrayerTimes(save = true) {
            const country = document.getElementById('countrySelect').value;
            const city = document.getElementById('citySelect').value;
            if(!country || !city) return;

            if (save) {
                localStorage.setItem('atheer_country', country);
                localStorage.setItem('atheer_city', city);
            }
            document.getElementById('locationDisplay').innerText = `${ARAB_COUNTRIES_DATA[country].name} - ${city}`;
            try {
                const url = `index.php?api_proxy=` + encodeURIComponent(`https://api.aladhan.com/v1/timingsByCity?city=${encodeURIComponent(city)}&country=${country}&method=4`);
                const res = await fetch(url);
                const data = await res.json();
                renderPrayerUI(data.data);
            } catch(e) { console.error(e); }
        }

        function renderPrayerUI(data) {
            const timings = data.timings;
            document.getElementById('gregorianDate').innerText = `${data.date.hijri.weekday.ar} | ${data.date.gregorian.date}`;
            document.getElementById('hijriDate').innerText = `${data.date.hijri.month.ar} ${data.date.hijri.year}`;
            
            const grid = document.getElementById('prayerGrid');
            grid.innerHTML = '';
            ["Fajr", "Sunrise", "Dhuhr", "Asr", "Maghrib", "Isha"].forEach(k => {
                const d = document.createElement('div');
                d.className = 'card';
                d.innerHTML = `<h3>${PRAYER_NAMES_AR[k]}</h3><p style="font-size:1.2rem; color:var(--primary);">${timings[k]}</p>`;
                grid.appendChild(d);
            });
            applyScrollReveal();
        }

        // 99 Names
        async function initAsma() {
            const grid = document.getElementById('asmaGrid');
            grid.innerHTML = '';
            try {
                const url = `index.php?api_proxy=` + encodeURIComponent('https://islamicapi.com/api/v1/asma-ul-husna/?language=ar&api_key=KVYgvSoKiJ8wabmIJY9d2zHHfPEvVqKgM19Kq3sAmZN8DvwW');
                const res = await fetch(url);
                const data = await res.json();
                data.data.names.forEach(item => {
                    const card = document.createElement('div');
                    card.className = 'card';
                    card.innerHTML = `<h3 style="font-family:'Amiri'; font-size:1.8rem; color:var(--primary);">${item.name}</h3><p>${item.meaning}</p>`;
                    grid.appendChild(card);
                });
                applyScrollReveal();
            } catch(e) { console.error(e); }
        }

        // Mushaf
        function initMushafControls() {
            const surahSel = document.getElementById('surahSelect');
            surahsData.forEach((s, i) => {
                const opt = document.createElement('option'); opt.value = s.p; opt.innerText = `${i+1}. ${s.n}`; surahSel.appendChild(opt);
            });
            document.getElementById('rightZone').onclick = () => state.pageFlipInstance?.flipPrev();
            document.getElementById('leftZone').onclick = () => state.pageFlipInstance?.flipNext();
        }

        async function openMushafOverlay() {
            document.getElementById('mushafOverlay').classList.add('active');
            if(!state.mushafLoaded) {
                const res = await fetch(MUSHAF_PAGES_API);
                const data = await res.json();
                const pages = data.pages.sort((a,b) => a.page_number - b.page_number);
                let html = pages.map(p => `<div class="page-content"><img src="${p.page_url}" onload="this.style.opacity=1;"></div>`).join('');
                document.getElementById('book').innerHTML = html;

                state.pageFlipInstance = new St.PageFlip(document.getElementById('book'), {
                    width: 450, height: 750, size: "stretch", showCover: true
                });
                state.pageFlipInstance.loadFromHTML(document.querySelectorAll('.page-content'));
                state.mushafLoaded = true;
            }
        }
        function closeMushafOverlay() { document.getElementById('mushafOverlay').classList.remove('active'); }
        function toggleMushafNavbar() { document.getElementById('mushafNavbar').classList.toggle('hidden-bar'); }

        // Audio & Controls logic
        function activatePlayerUI() { document.body.classList.add('player-active'); }
        function togglePlayState() { if(audio.paused) audio.play(); else audio.pause(); }
        function minimizePlayer() { document.getElementById('playerDock').classList.add('minimized'); }
        function restorePlayer() { document.getElementById('playerDock').classList.remove('minimized'); }
        function setVolume(v) { audio.volume = v; }
        function toggleMute() { audio.muted = !audio.muted; }
        function scrollToTop() { window.scrollTo({ top: 0, behavior: 'smooth' }); }

        function applyScrollReveal() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('reveal-active'); });
            }, { threshold: 0.1 });
            document.querySelectorAll('.card').forEach(c => observer.observe(c));
        }

        function isFav(type, id) { return favorites[type]?.some(x => (x.id || x.url || x.trackId) === id); }
        function toggleFav(e, type, data) {
            e.stopPropagation();
            // تبديل المفضلة
            renderFavoritesGrid();
        }
        function switchFavTab(t) { currentFavTab = t; renderFavoritesGrid(); }
        function renderFavoritesGrid() { /* منطق رسم المفضلة */ }
    </script>
</body>
</html>
