@php
$sidebarItems = [
    ['label' => 'Command Centre', 'icon' => 'fa-border-all', 'active' => true],
    ['label' => 'Dashboard', 'icon' => 'fa-chart-pie'],
    ['label' => 'Tasks', 'icon' => 'fa-check-square', 'badge' => 31],
    ['label' => 'Clients', 'icon' => 'fa-users'],
    ['label' => 'Compliance Centre', 'icon' => 'fa-shield-alt'],
    ['label' => 'Billing', 'icon' => 'fa-file-invoice-dollar'],
    ['label' => 'Practice Manager', 'icon' => 'fa-briefcase'],
    ['label' => 'Contacts', 'icon' => 'fa-address-book'],
    ['label' => 'Documents', 'icon' => 'fa-folder-open'],
    ['label' => 'Calendar', 'icon' => 'fa-calendar-alt'],
    ['label' => 'Reports & Insights', 'icon' => 'fa-chart-bar'],
    ['label' => 'Team', 'icon' => 'fa-user-friends'],
    ['label' => 'Settings', 'icon' => 'fa-cog'],
];

$tasks = [
    ['title'=>'Submit EMP201 Return','desc'=>'EMP201 for April 2026','category'=>'SARS','cat_color'=>'#3b82f6','client'=>'NexCore Africa (Pty) Ltd','priority'=>'Critical','pri_color'=>'#ef4444','assigned'=>'Krish Naidoo','initials'=>'KN','av_color'=>'#ef4444','due'=>'23 May 2026','status'=>'In Progress','st_color'=>'#3b82f6','countdown'=>'2 days','cd_color'=>'#f59e0b'],
    ['title'=>'CIPC Annual Return Filing','desc'=>'Annual return for NexCore Holdings','category'=>'CIPC','cat_color'=>'#10b981','client'=>'NexCore Holdings (Pty) Ltd','priority'=>'High','pri_color'=>'#f97316','assigned'=>'Shenell Govender','initials'=>'SG','av_color'=>'#8b5cf6','due'=>'27 May 2026','status'=>'To Do','st_color'=>'#6b7280','countdown'=>'6 days','cd_color'=>'#10b981'],
    ['title'=>'COIDA Return of Earnings','desc'=>'ROE for FY2025','category'=>'COIDA','cat_color'=>'#f59e0b','client'=>'NexCore Africa (Pty) Ltd','priority'=>'High','pri_color'=>'#f97316','assigned'=>'Pravesh Maharaj','initials'=>'PM','av_color'=>'#f59e0b','due'=>'25 May 2026','status'=>'To Do','st_color'=>'#6b7280','countdown'=>'4 days','cd_color'=>'#10b981'],
    ['title'=>'BEE Certificate Renewal','desc'=>'Certificate expires 30 June 2026','category'=>'Compliance','cat_color'=>'#06b6d4','client'=>'NexCore Africa (Pty) Ltd','priority'=>'Medium','pri_color'=>'#eab308','assigned'=>'Vanessa Naidoo','initials'=>'VN','av_color'=>'#3b82f6','due'=>'30 Jun 2026','status'=>'To Do','st_color'=>'#6b7280','countdown'=>'40 days','cd_color'=>'#10b981'],
    ['title'=>'ATP Services – Gavin','desc'=>'Birthday – Send Greeting','category'=>'Birthdays','cat_color'=>'#ec4899','client'=>'ATP Services (Pty) Ltd','priority'=>'Low','pri_color'=>'#6b7280','assigned'=>'Admin User','initials'=>'AU','av_color'=>'#6b7280','due'=>'21 May 2026','status'=>'To Do','st_color'=>'#6b7280','countdown'=>'Today','cd_color'=>'#ef4444'],
    ['title'=>'Monthly Management Accounts','desc'=>'Prepare and review','category'=>'General','cat_color'=>'#94a3b8','client'=>'ICCED (Pty) Ltd','priority'=>'Medium','pri_color'=>'#eab308','assigned'=>'Krish Naidoo','initials'=>'KN','av_color'=>'#ef4444','due'=>'31 May 2026','status'=>'In Progress','st_color'=>'#3b82f6','countdown'=>'10 days','cd_color'=>'#10b981'],
    ['title'=>'Client Review Meeting','desc'=>'Q2 Review & Planning','category'=>'Events','cat_color'=>'#8b5cf6','client'=>'Saint Cricket (Pty) Ltd','priority'=>'Low','pri_color'=>'#6b7280','assigned'=>'Shenell Govender','initials'=>'SG','av_color'=>'#8b5cf6','due'=>'23 May 2026','status'=>'Confirmed','st_color'=>'#10b981','countdown'=>'2 days','cd_color'=>'#f59e0b'],
];

$teamMembers = [
    ['name'=>'Krish Naidoo','initials'=>'KN','color'=>'#ef4444','workload'=>89],
    ['name'=>'Shenell Govender','initials'=>'SG','color'=>'#8b5cf6','workload'=>43],
    ['name'=>'Pravesh Maharaj','initials'=>'PM','color'=>'#f59e0b','workload'=>71],
    ['name'=>'Vanessa Naidoo','initials'=>'VN','color'=>'#3b82f6','workload'=>56],
    ['name'=>'Admin User','initials'=>'AU','color'=>'#6b7280','workload'=>32],
];

$activityFeed = [
    ['time'=>'09:45','text'=>'SARS EMP201 submitted for NexCore Africa','sub'=>'by Krish Naidoo','badge'=>'SARS','color'=>'#3b82f6','icon'=>'fa-file-invoice-dollar'],
    ['time'=>'10:01','text'=>'CIPC Annual Return due in 6 days','sub'=>'NexCore Holdings (Pty) Ltd','badge'=>'CIPC','color'=>'#10b981','icon'=>'fa-clipboard-check'],
    ['time'=>'10:35','text'=>'Birthday reminder – Gavin (ATP Services)','sub'=>'Today','badge'=>'Birthday','color'=>'#ec4899','icon'=>'fa-birthday-cake'],
    ['time'=>'11:15','text'=>'COIDA Assessment received','sub'=>'NexCore Africa (Pty) Ltd','badge'=>'COIDA','color'=>'#f59e0b','icon'=>'fa-hard-hat'],
    ['time'=>'11:30','text'=>'PAYE Run for May 2026 completed','sub'=>'2 clients processed','badge'=>'Payroll','color'=>'#6366f1','icon'=>'fa-money-check-alt'],
];

$complianceRadar = [
    ['name'=>'SARS','score'=>96,'color'=>'#3b82f6'],
    ['name'=>'CIPC','score'=>88,'color'=>'#10b981'],
    ['name'=>'COIDA','score'=>79,'color'=>'#f59e0b'],
    ['name'=>'PAYROLL','score'=>94,'color'=>'#6366f1'],
    ['name'=>'BEE','score'=>90,'color'=>'#22c55e'],
];

$categoryBreakdown = [
    ['name'=>'SARS','pct'=>28,'count'=>349,'color'=>'#3b82f6'],
    ['name'=>'CIPC','pct'=>18,'count'=>224,'color'=>'#10b981'],
    ['name'=>'COIDA','pct'=>14,'count'=>175,'color'=>'#f59e0b'],
    ['name'=>'Compliance','pct'=>20,'count'=>250,'color'=>'#06b6d4'],
    ['name'=>'Birthdays','pct'=>8,'count'=>100,'color'=>'#ec4899'],
    ['name'=>'Events','pct'=>7,'count'=>90,'color'=>'#8b5cf6'],
    ['name'=>'General','pct'=>5,'count'=>60,'color'=>'#94a3b8'],
];

$statusBreakdown = [
    ['name'=>'Completed','count'=>674,'pct'=>54,'color'=>'#10b981'],
    ['name'=>'In Progress','count'=>312,'pct'=>25,'color'=>'#3b82f6'],
    ['name'=>'To Do','count'=>180,'pct'=>14,'color'=>'#6b7280'],
    ['name'=>'Confirmed','count'=>51,'pct'=>4,'color'=>'#06b6d4'],
    ['name'=>'Overdue','count'=>31,'pct'=>3,'color'=>'#ef4444'],
];

$timelineCols = [
    ['label'=>'TODAY','date'=>'WED 21 MAY','items'=>[['name'=>'SARS EMP201','count'=>2,'color'=>'#3b82f6'],['name'=>'PAYE Submission','count'=>1,'color'=>'#6366f1'],['name'=>'Client Meeting','count'=>2,'color'=>'#8b5cf6']],'more'=>2],
    ['label'=>'TOMORROW','date'=>'THU 22 MAY','items'=>[['name'=>'CIPC Annual Returns','count'=>3,'color'=>'#10b981'],['name'=>'COIDA ROE','count'=>1,'color'=>'#f59e0b'],['name'=>'ATP Birthday','count'=>1,'color'=>'#ec4899']],'more'=>0],
    ['label'=>'IN 2 DAYS','date'=>'FRI 23 MAY','items'=>[['name'=>'VAT201 Submission','count'=>2,'color'=>'#f59e0b'],['name'=>'Unions/MIBCO','count'=>1,'color'=>'#8b5cf6']],'more'=>0],
    ['label'=>'THIS WEEK','date'=>'SAT 24 MAY – SUN 25 MAY','items'=>[['name'=>'COIDA Assessment','count'=>2,'color'=>'#f59e0b'],['name'=>'BEE Certificate Expiry','count'=>1,'color'=>'#06b6d4']],'more'=>3],
    ['label'=>'NEXT WEEK','date'=>'26 MAY – 1 JUN','items'=>[['name'=>'Annual Financials','count'=>2,'color'=>'#8b5cf6'],['name'=>'Board Meeting','count'=>1,'color'=>'#94a3b8']],'more'=>0],
];

$calendarWeeks = [
    [['d'=>28,'o'=>1],['d'=>29,'o'=>1],['d'=>30,'o'=>1],['d'=>1],['d'=>2],['d'=>3],['d'=>4]],
    [['d'=>5],['d'=>6],['d'=>7,'dots'=>['#3b82f6','#06b6d4']],['d'=>8],['d'=>9,'dots'=>['#8b5cf6']],['d'=>10],['d'=>11]],
    [['d'=>12],['d'=>13],['d'=>14,'dots'=>['#10b981']],['d'=>15,'dots'=>['#f59e0b','#3b82f6']],['d'=>16,'dots'=>['#06b6d4']],['d'=>17],['d'=>18]],
    [['d'=>19],['d'=>20],['d'=>21,'today'=>true,'dots'=>['#3b82f6','#ec4899','#8b5cf6']],['d'=>22,'dots'=>['#10b981','#f59e0b']],['d'=>23,'dots'=>['#3b82f6','#8b5cf6']],['d'=>24],['d'=>25,'dots'=>['#f59e0b']]],
    [['d'=>26],['d'=>27,'dots'=>['#10b981']],['d'=>28],['d'=>29],['d'=>30,'dots'=>['#06b6d4']],['d'=>31,'dots'=>['#94a3b8']],['d'=>1,'o'=>1]],
];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>NexCore | Command Centre</title>
<link rel="shortcut icon" type="image/png" href="/public/smartdash/images/favicon.png">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
--cc-bg:#070b16;--cc-sidebar-bg:#0c1222;--cc-surface:#111827;--cc-surface2:#1a2332;
--cc-border:rgba(255,255,255,0.06);--cc-border2:rgba(255,255,255,0.1);
--cc-text:#f1f5f9;--cc-dim:#b0bec5;--cc-muted:#8896a5;
--cc-green:#10b981;--cc-red:#ef4444;--cc-blue:#3b82f6;--cc-purple:#8b5cf6;
--cc-amber:#f59e0b;--cc-cyan:#06b6d4;--cc-pink:#ec4899;--cc-indigo:#6366f1;
--sidebar-w:220px;
}
html{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
body{font-family:'Montserrat',sans-serif;background:var(--cc-bg);color:var(--cc-text);height:100vh;overflow:hidden;display:flex}

/* ══ SIDEBAR ══ */
.cc-sb{width:var(--sidebar-w);height:100vh;background:var(--cc-sidebar-bg);border-right:1px solid var(--cc-border);display:flex;flex-direction:column;flex-shrink:0;overflow:hidden}
.cc-sb-logo{padding:18px 20px;display:flex;align-items:center;gap:12px;border-bottom:1px solid var(--cc-border)}
.cc-sb-hex{width:38px;height:38px;background:linear-gradient(135deg,var(--cc-green),var(--cc-cyan));clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.cc-sb-hex i{color:#fff;font-size:14px}
.cc-sb-brand{display:flex;flex-direction:column}
.cc-sb-brand span:first-child{font-size:14px;font-weight:800;letter-spacing:1.5px;color:#fff}
.cc-sb-brand span:last-child{font-size:9px;font-weight:600;letter-spacing:0.8px;color:var(--cc-muted);text-transform:uppercase;margin-top:1px}
.cc-sb-nav{flex:1;overflow-y:auto;padding:12px 0}
.cc-sb-nav::-webkit-scrollbar{width:0}
.cc-sb-item{display:flex;align-items:center;gap:12px;padding:11px 20px;font-size:13px;font-weight:500;color:var(--cc-dim);cursor:pointer;transition:all 0.2s;position:relative;text-decoration:none}
.cc-sb-item:hover{color:var(--cc-text);background:rgba(255,255,255,0.03)}
.cc-sb-item.active{color:#fff;background:rgba(16,185,129,0.08)}
.cc-sb-item.active::before{content:'';position:absolute;left:0;top:6px;bottom:6px;width:3px;background:var(--cc-green);border-radius:0 3px 3px 0}
.cc-sb-item i{width:20px;text-align:center;font-size:14px}
.cc-sb-item.active i{color:var(--cc-green)}
.cc-sb-badge{margin-left:auto;background:var(--cc-red);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;min-width:20px;text-align:center}
.cc-sb-footer{padding:16px 20px;border-top:1px solid var(--cc-border)}
.cc-sb-dark{display:flex;align-items:center;gap:10px;font-size:12px;color:var(--cc-dim);margin-bottom:16px}
.cc-sb-toggle{width:36px;height:20px;background:var(--cc-green);border-radius:10px;position:relative;margin-left:auto;cursor:pointer}
.cc-sb-toggle::after{content:'';width:16px;height:16px;background:#fff;border-radius:50%;position:absolute;top:2px;right:2px;transition:0.2s}
.cc-sb-copy{display:flex;flex-direction:column;align-items:center;gap:4px;padding-top:12px;border-top:1px solid var(--cc-border)}
.cc-sb-copy img{height:22px;opacity:0.5}
.cc-sb-copy .cc-sb-tagline{font-size:9px;color:var(--cc-muted);letter-spacing:0.5px}
.cc-sb-copy .cc-sb-cr{font-size:9px;color:var(--cc-muted);opacity:0.5}

/* ══ MAIN ══ */
.cc-main{flex:1;display:flex;flex-direction:column;overflow:hidden}

/* ══ TOPBAR ══ */
.cc-top{height:58px;background:var(--cc-sidebar-bg);border-bottom:1px solid var(--cc-border);display:flex;align-items:center;padding:0 24px;gap:20px;flex-shrink:0}
.cc-back-btn{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--cc-dim);text-decoration:none;padding:7px 14px;border-radius:8px;border:1px solid var(--cc-border);transition:all 0.2s;white-space:nowrap;font-family:'Montserrat',sans-serif}
.cc-back-btn:hover{color:#fff;background:rgba(255,255,255,0.06);border-color:rgba(255,255,255,0.15)}
.cc-back-btn i{font-size:11px}
.cc-top-title{display:flex;flex-direction:column}
.cc-top-title h1{font-size:18px;font-weight:700;color:#fff;line-height:1.2}
.cc-top-title span{font-size:10px;color:var(--cc-muted);letter-spacing:0.8px;font-weight:500}
.cc-top-spacer{flex:1}
.cc-top-search{display:flex;align-items:center;background:var(--cc-surface);border:1px solid var(--cc-border);border-radius:8px;padding:7px 14px;gap:8px;min-width:240px}
.cc-top-search i{color:var(--cc-muted);font-size:13px}
.cc-top-search span{color:var(--cc-muted);font-size:12px;flex:1}
.cc-top-search kbd{background:var(--cc-surface2);color:var(--cc-muted);font-size:10px;padding:2px 6px;border-radius:4px;border:1px solid var(--cc-border);font-family:'Montserrat',sans-serif}
.cc-top-quick{background:var(--cc-green);color:#fff;border:none;padding:8px 16px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;font-family:'Montserrat',sans-serif}
.cc-top-quick:hover{filter:brightness(1.1)}
.cc-top-icon{width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:8px;background:rgba(255,255,255,0.04);color:var(--cc-dim);font-size:15px;cursor:pointer;position:relative;transition:0.2s}
.cc-top-icon:hover{background:rgba(255,255,255,0.08);color:var(--cc-text)}
.cc-top-notif{position:absolute;top:-2px;right:-2px;background:var(--cc-red);color:#fff;font-size:9px;font-weight:700;width:17px;height:17px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:2px solid var(--cc-sidebar-bg)}
.cc-top-user{display:flex;align-items:center;gap:10px;margin-left:4px}
.cc-top-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--cc-purple),var(--cc-blue));display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff}
.cc-top-uinfo{display:flex;flex-direction:column}
.cc-top-uinfo span:first-child{font-size:12px;font-weight:600;color:#fff}
.cc-top-uinfo span:last-child{font-size:10px;color:var(--cc-muted)}

/* ══ CONTENT ══ */
.cc-content{flex:1;overflow-y:auto;padding:16px 20px;display:flex;flex-direction:column;gap:14px}
.cc-content>div,.cc-content>table{flex-shrink:0;min-width:0}
.cc-content::-webkit-scrollbar{width:6px}
.cc-content::-webkit-scrollbar-track{background:transparent}
.cc-content::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.08);border-radius:3px}

/* ══ SUMMARY CARDS ══ */
.cc-cards{display:grid;grid-template-columns:repeat(6,1fr);gap:14px}
.cc-card{background:var(--cc-surface);border:1px solid var(--cc-border);border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:14px;position:relative;overflow:hidden;transition:0.2s}
.cc-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;border-radius:12px 12px 0 0}
.cc-card:hover{border-color:var(--cc-border2);transform:translateY(-1px)}
.cc-card-icon{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.cc-card-info{flex:1;min-width:0}
.cc-card-label{font-size:10px;font-weight:600;letter-spacing:0.8px;color:var(--cc-muted);text-transform:uppercase;margin-bottom:2px}
.cc-card-val{font-size:28px;font-weight:800;color:#fff;line-height:1.1;letter-spacing:2.5px}
.cc-card-val small{font-size:16px;font-weight:500;color:var(--cc-dim)}
.cc-card-change{font-size:13px;font-weight:500;margin-top:2px}
.cc-card-change.up{color:var(--cc-green)}
.cc-card-change.down{color:var(--cc-red)}
.cc-card-change.label-healthy{color:var(--cc-green);font-weight:600}
.cc-card-change.label-excellent{color:var(--cc-green);font-weight:600}

/* ══ MID ROW: CALENDAR + ACTIVITY + WORKLOAD ══ */
.cc-mid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px}
.cc-panel{background:var(--cc-surface);border:1px solid var(--cc-border);border-radius:12px;overflow:hidden}
.cc-panel-head{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--cc-border)}
.cc-panel-title{font-size:17px;font-weight:700;letter-spacing:0.8px;text-transform:uppercase;color:var(--cc-text)}
.cc-toggle-group{display:flex;background:var(--cc-bg);border-radius:6px;overflow:hidden;border:1px solid var(--cc-border)}
.cc-toggle-btn{padding:5px 12px;font-size:15px;font-weight:600;color:var(--cc-muted);cursor:pointer;border:none;background:none;font-family:'Montserrat',sans-serif;transition:0.2s}
.cc-toggle-btn.active{background:var(--cc-blue);color:#fff}

/* Timeline */
.cc-tl-body{display:grid;grid-template-columns:repeat(5,1fr);padding:0;min-height:150px}
.cc-tl-col{padding:10px 12px;border-right:1px solid var(--cc-border);display:flex;flex-direction:column;gap:6px}
.cc-tl-col:last-child{border-right:none}
.cc-tl-head{margin-bottom:4px}
.cc-tl-head .cc-tl-lbl{font-size:15px;font-weight:700;color:var(--cc-amber);letter-spacing:0.5px;text-transform:uppercase}
.cc-tl-head .cc-tl-date{font-size:14px;color:var(--cc-muted);margin-top:1px}
.cc-tl-item{display:flex;align-items:center;gap:6px;padding:5px 8px;background:rgba(255,255,255,0.02);border-radius:6px;border:1px solid var(--cc-border);transition:all 0.2s ease}
.cc-tl-item:hover{background:rgba(255,255,255,0.06);border-color:var(--tl-color);box-shadow:0 2px 12px -3px var(--tl-color);transform:scale(1.02)}
.cc-tl-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}
.cc-tl-name{font-size:17px;font-weight:500;color:var(--cc-dim);flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.cc-tl-count{font-size:14px;font-weight:600;color:var(--cc-muted);background:rgba(255,255,255,0.05);padding:1px 6px;border-radius:8px;white-space:nowrap}
.cc-tl-more{font-size:15px;color:var(--cc-muted);padding:2px 0;cursor:pointer}
.cc-tl-more:hover{color:var(--cc-text)}

/* Calendar */
.cc-cal-head{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid var(--cc-border)}
.cc-cal-month{font-size:13px;font-weight:700;color:var(--cc-text);display:flex;align-items:center;gap:4px;cursor:pointer}
.cc-cal-month i{font-size:10px;color:var(--cc-muted)}
.cc-cal-nav{display:flex;align-items:center;gap:6px}
.cc-cal-nav button{width:26px;height:26px;border-radius:6px;border:1px solid var(--cc-border);background:none;color:var(--cc-dim);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:11px;transition:0.15s}
.cc-cal-nav button:hover{background:rgba(255,255,255,0.05);color:var(--cc-text)}
.cc-cal-today{font-size:11px;font-weight:600;color:var(--cc-cyan);cursor:pointer;background:none;border:none;font-family:'Montserrat',sans-serif}
.cc-cal-grid{display:grid;grid-template-columns:repeat(7,1fr);padding:6px 10px 8px}
.cc-cal-dow{font-size:9px;font-weight:600;color:var(--cc-muted);text-align:center;padding:4px 0;text-transform:uppercase;letter-spacing:0.5px}
.cc-cal-day{text-align:center;padding:4px 0;position:relative;cursor:pointer}
.cc-cal-day span{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;font-size:11px;font-weight:500;color:var(--cc-dim);border-radius:50%;transition:0.15s}
.cc-cal-day:hover span{background:rgba(255,255,255,0.06)}
.cc-cal-day.other span{color:var(--cc-muted);opacity:0.4}
.cc-cal-day.today span{background:var(--cc-cyan);color:#fff;font-weight:700}
.cc-cal-dots{display:flex;gap:2px;justify-content:center;margin-top:1px;min-height:5px}
.cc-cal-dots i{width:4px;height:4px;border-radius:50%;display:block}
.cc-cal-legend{display:flex;gap:12px;padding:6px 14px;border-top:1px solid var(--cc-border);flex-wrap:wrap}
.cc-cal-legend-item{display:flex;align-items:center;gap:4px;font-size:9px;color:var(--cc-muted);font-weight:500}
.cc-cal-legend-dot{width:6px;height:6px;border-radius:50%}

/* ══ FILTER BAR ══ */
.cc-filters{display:flex;align-items:center;gap:8px;flex-wrap:wrap;background:var(--cc-surface);border:1px solid var(--cc-border);border-radius:12px;padding:12px 16px}
.cc-filter-dd{display:flex;align-items:center;gap:6px;background:var(--cc-surface2);border:1px solid var(--cc-border);border-left:2px solid var(--cc-cyan);border-radius:8px;padding:8px 14px;cursor:pointer;transition:0.2s}
.cc-filter-dd:hover{border-color:var(--cc-cyan);background:rgba(6,182,212,0.06)}
.cc-filter-dd .cc-f-label{font-size:14px;color:var(--cc-cyan);font-weight:500}
.cc-filter-dd .cc-f-val{font-size:15px;color:var(--cc-text);font-weight:600;margin-left:2px}
.cc-filter-dd i{font-size:11px;color:var(--cc-cyan);margin-left:2px}
.cc-filter-search{display:flex;align-items:center;gap:6px;background:var(--cc-surface2);border:1px solid var(--cc-border);border-left:2px solid var(--cc-green);border-radius:8px;padding:8px 14px;flex:1;min-width:120px}
.cc-filter-search i{color:var(--cc-green);font-size:14px}
.cc-filter-search span{color:var(--cc-muted);font-size:13px}
.cc-filter-btn{display:flex;align-items:center;gap:5px;background:var(--cc-surface2);border:1px solid var(--cc-border);border-left:2px solid var(--cc-amber);border-radius:8px;padding:8px 14px;cursor:pointer;font-size:13px;font-weight:600;color:var(--cc-amber);transition:0.2s}
.cc-filter-btn:hover{border-color:var(--cc-amber);background:rgba(245,158,11,0.06);color:var(--cc-text)}
.cc-filter-btn i{color:var(--cc-amber)}
.cc-view-toggles{display:flex;border:1px solid var(--cc-border);border-radius:8px;overflow:hidden}
.cc-view-btn{width:34px;height:34px;display:flex;align-items:center;justify-content:center;background:var(--cc-surface2);color:var(--cc-muted);cursor:pointer;border:none;font-size:15px;transition:0.2s}
.cc-view-btn.active{background:var(--cc-green);color:#fff}
.cc-view-btn:hover:not(.active){background:rgba(255,255,255,0.06);color:var(--cc-text)}
.cc-view-btn+.cc-view-btn{border-left:1px solid var(--cc-border)}

/* ══ TASK TABLE ══ */
.cc-table-wrap{background:var(--cc-surface);border:1px solid var(--cc-border);border-radius:12px;overflow-x:auto;overflow-y:hidden;min-width:0;flex-shrink:0}
.cc-tbl{width:100%;border-collapse:collapse;table-layout:fixed}
.cc-tbl thead th{font-size:10px;font-weight:600;color:var(--cc-muted);text-transform:uppercase;letter-spacing:0.5px;padding:10px 12px;text-align:left;border-bottom:1px solid var(--cc-border);white-space:nowrap}
.cc-tbl thead th.sortable{cursor:pointer}
.cc-tbl thead th.sortable i{margin-left:3px;font-size:9px}
.cc-tbl tbody tr{border-bottom:1px solid var(--cc-border);transition:0.15s}
.cc-tbl tbody tr:last-child{border-bottom:none}
.cc-tbl tbody tr:hover{background:rgba(255,255,255,0.02)}
.cc-tbl td{padding:10px 12px;font-size:14px;vertical-align:middle;white-space:nowrap}
.cc-tbl .cc-t-num{color:var(--cc-muted);font-size:11px;width:30px}
.cc-tbl .cc-t-expand{width:24px;color:var(--cc-muted);cursor:pointer;font-size:11px}
.cc-tbl .cc-t-expand:hover{color:var(--cc-text)}
.cc-t-task{max-width:220px}
.cc-t-task-title{font-size:14px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;display:block}
.cc-t-task-desc{font-size:12px;color:var(--cc-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;display:block;margin-top:1px}
.cc-badge{display:inline-block;padding:3px 10px;border-radius:6px;font-size:10px;font-weight:600;letter-spacing:0.3px}
.cc-t-client{font-size:13px;color:var(--cc-dim);max-width:180px;overflow:hidden;text-overflow:ellipsis}
.cc-t-pri{display:flex;align-items:center;gap:5px;font-size:13px;font-weight:600}
.cc-t-pri i{font-size:10px}
.cc-t-assigned{display:flex;align-items:center;gap:8px}
.cc-t-av{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;color:#fff;flex-shrink:0}
.cc-t-av-name{font-size:13px;color:var(--cc-dim)}
.cc-t-due{font-size:13px;color:var(--cc-dim)}
.cc-t-cd{display:inline-block;padding:3px 10px;border-radius:6px;font-size:10px;font-weight:600}

/* ══ BOTTOM PANELS ══ */
.cc-bottom{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px}
.cc-bot-panel{background:var(--cc-surface);border:1px solid var(--cc-border);border-radius:12px;display:flex;flex-direction:column;overflow:hidden}
.cc-bot-head{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--cc-border)}
.cc-bot-title{font-size:11px;font-weight:700;letter-spacing:0.6px;text-transform:uppercase;color:var(--cc-text)}
.cc-bot-link{font-size:11px;color:var(--cc-red);font-weight:600;cursor:pointer;text-decoration:none}
.cc-bot-link:hover{text-decoration:underline}
.cc-bot-dd{font-size:10px;color:var(--cc-dim);display:flex;align-items:center;gap:4px;cursor:pointer}
.cc-bot-body{flex:1;padding:10px 14px;overflow-y:auto}
.cc-bot-body::-webkit-scrollbar{width:4px}
.cc-bot-body::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.06);border-radius:2px}

/* Activity Feed */
.cc-act-item{display:flex;align-items:flex-start;gap:10px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,0.03)}
.cc-act-item:last-child{border-bottom:none}
.cc-act-time{font-size:10px;font-weight:600;color:var(--cc-muted);min-width:36px;padding-top:3px}
.cc-act-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0}
.cc-act-text{flex:1;min-width:0}
.cc-act-text span{font-size:13px;color:var(--cc-dim);display:block;line-height:1.3}
.cc-act-text .cc-act-sub{font-size:12px;color:var(--cc-muted);margin-top:1px}
.cc-act-badge{font-size:9px;font-weight:600;padding:2px 8px;border-radius:4px;white-space:nowrap;flex-shrink:0;align-self:center}

/* Team Workload */
.cc-wl-item{display:flex;align-items:center;gap:10px;padding:8px 0}
.cc-wl-av{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;flex-shrink:0}
.cc-wl-name{font-size:13px;color:var(--cc-dim);min-width:110px}
.cc-wl-bar{flex:1;height:8px;background:rgba(255,255,255,0.06);border-radius:4px;overflow:hidden}
.cc-wl-fill{height:100%;border-radius:4px;transition:width 0.6s ease}
.cc-wl-pct{font-size:13px;font-weight:600;color:var(--cc-dim);min-width:36px;text-align:right}

/* Compliance Radar */
.cc-radar-visual{display:flex;flex-direction:row;align-items:center;gap:24px;padding:12px 0}
.cc-radar-ring{width:160px;height:160px;position:relative;flex-shrink:0}
.cc-radar-shield{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:56px;height:56px;border-radius:50%;background:rgba(6,182,212,0.1);display:flex;align-items:center;justify-content:center}
.cc-radar-shield i{font-size:24px;color:var(--cc-cyan)}
.cc-radar-list{flex:1}
.cc-radar-item{display:flex;align-items:center;gap:10px;padding:6px 0}
.cc-radar-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
.cc-radar-name{font-size:15px;color:var(--cc-dim);flex:1}
.cc-radar-score{font-size:18px;font-weight:700;color:var(--cc-text)}

/* Donut Chart */
.cc-donut-wrap{display:flex;align-items:center;gap:28px;padding:12px 0}
.cc-donut-ring{width:180px;height:180px;border-radius:50%;position:relative;flex-shrink:0;background:conic-gradient(#3b82f6 0deg 100.8deg,#10b981 100.8deg 165.6deg,#f59e0b 165.6deg 216deg,#06b6d4 216deg 288deg,#ec4899 288deg 316.8deg,#8b5cf6 316.8deg 342deg,#94a3b8 342deg 360deg)}
.cc-donut-hole{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:115px;height:115px;border-radius:50%;background:var(--cc-surface);display:flex;flex-direction:column;align-items:center;justify-content:center}
.cc-donut-hole .cc-dn-num{font-size:26px;font-weight:800;color:#fff;line-height:1;letter-spacing:2px}
.cc-donut-hole .cc-dn-lbl{font-size:10px;color:var(--cc-muted);margin-top:3px;font-weight:500}
.cc-donut-legend{display:flex;flex-direction:column;gap:6px;flex:1}
.cc-donut-legend-item{display:flex;align-items:center;gap:8px;font-size:15px}
.cc-dl-dot{width:10px;height:10px;border-radius:3px;flex-shrink:0}
.cc-dl-name{color:var(--cc-dim);flex:1}
.cc-dl-val{color:var(--cc-muted);font-weight:600}
.cc-dl-count{color:var(--cc-muted);font-size:12px}

/* Status Breakdown */
.cc-st-item{display:flex;align-items:center;gap:10px;padding:7px 0}
.cc-st-dot{width:10px;height:10px;border-radius:3px;flex-shrink:0}
.cc-st-name{font-size:15px;color:var(--cc-dim);min-width:100px}
.cc-st-bar{flex:1;height:10px;background:rgba(255,255,255,0.06);border-radius:5px;overflow:hidden}
.cc-st-fill{height:100%;border-radius:5px;transition:width 0.6s ease}
.cc-st-count{font-size:15px;font-weight:700;color:var(--cc-text);min-width:40px;text-align:right}
.cc-st-pct{font-size:13px;font-weight:500;color:var(--cc-muted);min-width:36px;text-align:right}

/* ══ SLIDE-OUT DRAWER ══ */
.cc-drawer-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:transparent;z-index:998;opacity:0;visibility:hidden;transition:all 0.3s ease;pointer-events:none}
.cc-drawer-overlay.open{opacity:1;visibility:visible;pointer-events:auto}
.cc-drawer{position:fixed;top:0;right:-460px;width:460px;height:100vh;background:linear-gradient(180deg,#2a1e08 0%,#1a1206 100%);border-left:3px solid rgba(245,158,11,0.5);border-top:4px solid var(--cc-amber);z-index:999;display:flex;flex-direction:column;transition:right 0.3s ease;box-shadow:-10px 0 50px rgba(245,158,11,0.2),-3px 0 20px rgba(245,158,11,0.15)}
.cc-drawer.open{right:0}
.cc-drawer-head{display:flex;align-items:center;gap:12px;padding:18px 20px;border-bottom:1px solid rgba(245,158,11,0.25);flex-shrink:0;background:rgba(245,158,11,0.1)}
.cc-drawer-cat-dot{width:12px;height:12px;border-radius:50%;flex-shrink:0}
.cc-drawer-title{flex:1}
.cc-drawer-title h3{font-size:16px;font-weight:700;color:#fff;margin-bottom:2px}
.cc-drawer-title span{font-size:12px;color:var(--cc-muted)}
.cc-drawer-close{width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,0.05);border:1px solid var(--cc-border);color:var(--cc-dim);display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:14px;transition:0.2s}
.cc-drawer-close:hover{background:rgba(239,68,68,0.1);border-color:var(--cc-red);color:var(--cc-red)}
.cc-drawer-body{flex:1;overflow-y:auto;padding:14px 20px;display:flex;flex-direction:column;gap:12px}
.cc-drawer-body::-webkit-scrollbar{width:5px}
.cc-drawer-body::-webkit-scrollbar-thumb{background:rgba(255,255,255,0.08);border-radius:3px}
.cc-dtask{background:rgba(245,158,11,0.06);border:1px solid rgba(245,158,11,0.15);border-radius:10px;padding:14px 16px;transition:0.2s}
.cc-dtask:hover{border-color:rgba(245,158,11,0.3);background:rgba(245,158,11,0.1)}
.cc-dtask-top{display:flex;align-items:flex-start;gap:10px;margin-bottom:10px}
.cc-dtask-num{font-size:11px;font-weight:700;color:var(--cc-muted);min-width:20px;padding-top:2px}
.cc-dtask-info{flex:1;min-width:0}
.cc-dtask-name{font-size:15px;font-weight:600;color:#fff;margin-bottom:2px}
.cc-dtask-desc{font-size:12px;color:var(--cc-muted)}
.cc-dtask-status{padding:3px 10px;border-radius:6px;font-size:10px;font-weight:600;white-space:nowrap;flex-shrink:0}
.cc-dtask-meta{display:flex;align-items:center;gap:14px;padding:8px 0;border-top:1px solid var(--cc-border);border-bottom:1px solid var(--cc-border);margin-bottom:10px;flex-wrap:wrap}
.cc-dtask-meta-item{display:flex;align-items:center;gap:6px;font-size:12px;color:var(--cc-dim)}
.cc-dtask-meta-item i{font-size:11px;color:var(--cc-muted)}
.cc-dtask-av{width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:700;color:#fff;flex-shrink:0}
.cc-dtask-actions{display:flex;gap:6px;flex-wrap:wrap}
.cc-dtask-btn{display:flex;align-items:center;gap:5px;padding:6px 12px;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:0.2s;border:1px solid var(--cc-border);background:var(--cc-surface2);color:var(--cc-dim);font-family:'Montserrat',sans-serif;position:relative}
.cc-dtask-btn:hover{border-color:var(--cc-border2);color:var(--cc-text)}
.cc-dtask-btn.primary{background:rgba(16,185,129,0.1);border-color:rgba(16,185,129,0.3);color:var(--cc-green)}
.cc-dtask-btn.primary:hover{background:rgba(16,185,129,0.2)}
.cc-dtask-btn i{font-size:10px}
.cc-dd-menu{position:absolute;top:100%;left:0;margin-top:4px;background:var(--cc-surface2);border:1px solid var(--cc-border2);border-radius:8px;padding:4px;min-width:160px;z-index:10;display:none;box-shadow:0 8px 24px rgba(0,0,0,0.4)}
.cc-dd-menu.show{display:block}
.cc-dd-opt{display:flex;align-items:center;gap:8px;padding:7px 10px;font-size:12px;color:var(--cc-dim);border-radius:5px;cursor:pointer;transition:0.15s}
.cc-dd-opt:hover{background:rgba(255,255,255,0.06);color:var(--cc-text)}
.cc-dd-opt i{font-size:10px;width:16px;text-align:center}
.cc-dd-opt .cc-dd-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.cc-msg-box{margin-top:8px;display:none}
.cc-msg-box.show{display:block}
.cc-msg-input{width:100%;background:var(--cc-bg);border:1px solid var(--cc-border);border-radius:8px;padding:8px 12px;color:var(--cc-text);font-size:12px;font-family:'Montserrat',sans-serif;resize:none;outline:none}
.cc-msg-input:focus{border-color:var(--cc-cyan)}
.cc-msg-send{margin-top:6px;padding:6px 14px;background:var(--cc-cyan);color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;font-family:'Montserrat',sans-serif}

/* ══ ANIMATIONS ══ */
@@keyframes ccPulse{0%,100%{opacity:1}50%{opacity:0.6}}
.cc-pulse{animation:ccPulse 2s ease-in-out infinite}
@@keyframes ccSlideIn{from{transform:translateY(8px);opacity:0.3}to{transform:translateY(0);opacity:1}}
.cc-anim{animation:ccSlideIn 0.35s ease forwards}
.cc-d1{animation-delay:0.03s}.cc-d2{animation-delay:0.06s}.cc-d3{animation-delay:0.09s}
.cc-d4{animation-delay:0.12s}.cc-d5{animation-delay:0.15s}.cc-d6{animation-delay:0.18s}
</style>
</head>
<body>

{{-- ═══════════════ MAIN (FULL WIDTH - NO SIDEBAR) ═══════════════ --}}
<div class="cc-main">

    {{-- TOP BAR --}}
    <header class="cc-top">
        <a href="/nexcore/clients" class="cc-back-btn"><i class="fas fa-arrow-left"></i> Clients</a>
        <div class="cc-top-title">
            <h1>NexCore Command Centre</h1>
            <span>Everything. Prioritised. Actionable.</span>
        </div>
        <div class="cc-top-spacer"></div>
        <div class="cc-top-search">
            <i class="fas fa-search"></i>
            <span>Search everything...</span>
            <kbd>CTRL + K</kbd>
        </div>
        <button class="cc-top-quick"><i class="fas fa-plus"></i> Quick Add</button>
        <div class="cc-top-icon">
            <i class="fas fa-bell"></i>
            <span class="cc-top-notif">7</span>
        </div>
        <div class="cc-top-icon"><i class="fas fa-calendar-alt"></i></div>
        <div class="cc-top-user">
            <div class="cc-top-avatar">KN</div>
            <div class="cc-top-uinfo">
                <span>Krish Naidoo</span>
                <span>Super Admin</span>
            </div>
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="cc-content">

        {{-- ══ ROW 1: SUMMARY CARDS ══ --}}
        <div class="cc-cards">
            @php
            $cardMeta = [
                ['icon'=>'fa-clipboard-list','color'=>'#3b82f6','val'=>'1 248','label'=>'TOTAL TASKS','change'=>'↑ 12% vs last month','ctype'=>'up'],
                ['icon'=>'fa-exclamation-triangle','color'=>'#ef4444','val'=>'31','label'=>'OVERDUE','change'=>'↓ 5 vs last month','ctype'=>'down'],
                ['icon'=>'fa-calendar-check','color'=>'#10b981','val'=>'85','label'=>'DUE THIS WEEK','change'=>'↑ 14 vs last week','ctype'=>'up'],
                ['icon'=>'fa-check-circle','color'=>'#10b981','val'=>'674','label'=>'COMPLETED THIS MONTH','change'=>'↑ 8% vs last month','ctype'=>'up'],
                ['icon'=>'fa-shield-alt','color'=>'#06b6d4','val'=>'97.2%','label'=>'COMPLIANCE HEALTH','change'=>'Healthy','ctype'=>'label-healthy'],
                ['icon'=>'fa-star','color'=>'#f59e0b','val'=>'4.8','suffix'=>'/5','label'=>'CLIENT SATISFACTION','change'=>'Excellent','ctype'=>'label-excellent'],
            ];
            @endphp
            @foreach($cardMeta as $ci => $c)
            <div class="cc-card cc-anim cc-d{{ $ci + 1 }}" style="border-top:2px solid {{ $c['color'] }};">
                <div class="cc-card-icon" style="background:{{ $c['color'] }}22;color:{{ $c['color'] }};">
                    <i class="fas {{ $c['icon'] }}"></i>
                </div>
                <div class="cc-card-info">
                    <div class="cc-card-label">{{ $c['label'] }}</div>
                    <div class="cc-card-val">{{ $c['val'] }}@if(!empty($c['suffix']))<small>{{ $c['suffix'] }}</small>@endif</div>
                    <div class="cc-card-change {{ $c['ctype'] }}">{{ $c['change'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ══ ROW 2: TIMELINE (FULL WIDTH) ══ --}}
        <div class="cc-panel cc-anim cc-d3">
            <div class="cc-panel-head">
                <span class="cc-panel-title">Upcoming Deadlines Timeline</span>
                <div class="cc-toggle-group">
                    <button class="cc-toggle-btn">Day</button>
                    <button class="cc-toggle-btn active">Week</button>
                    <button class="cc-toggle-btn">Month</button>
                </div>
            </div>
            <div class="cc-tl-body">
                @foreach($timelineCols as $col)
                <div class="cc-tl-col">
                    <div class="cc-tl-head">
                        @if($col['label'])<div class="cc-tl-lbl">{{ $col['label'] }}</div>@endif
                        <div class="cc-tl-date">{{ $col['date'] }}</div>
                    </div>
                    @foreach($col['items'] as $ti)
                    <div class="cc-tl-item" style="--tl-color:{{ $ti['color'] }};">
                        <span class="cc-tl-dot" style="background:{{ $ti['color'] }};"></span>
                        <span class="cc-tl-name">{{ $ti['name'] }}</span>
                        <span class="cc-tl-count">{{ $ti['count'] }} {{ $ti['count'] == 1 ? 'task' : 'tasks' }}</span>
                    </div>
                    @endforeach
                    @if($col['more'] > 0)
                    <span class="cc-tl-more">+{{ $col['more'] }} more</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        {{-- ══ ROW 3: FILTER BAR ══ --}}
        <div class="cc-filters cc-anim cc-d4">
            <div class="cc-filter-dd"><span class="cc-f-label">Category</span><span class="cc-f-val">All</span><i class="fas fa-chevron-down"></i></div>
            <div class="cc-filter-dd"><span class="cc-f-label">Status</span><span class="cc-f-val">All</span><i class="fas fa-chevron-down"></i></div>
            <div class="cc-filter-dd"><span class="cc-f-label">Priority</span><span class="cc-f-val">All</span><i class="fas fa-chevron-down"></i></div>
            <div class="cc-filter-dd"><span class="cc-f-label">Assigned To</span><span class="cc-f-val">All</span><i class="fas fa-chevron-down"></i></div>
            <div class="cc-filter-dd"><span class="cc-f-label">Client</span><span class="cc-f-val">All</span><i class="fas fa-chevron-down"></i></div>
            <div class="cc-filter-dd"><span class="cc-f-label">Date Range</span><span class="cc-f-val">This Month</span><i class="fas fa-chevron-down"></i></div>
            <div class="cc-filter-search"><i class="fas fa-search"></i><span>Search tasks...</span></div>
            <div class="cc-filter-btn"><i class="fas fa-layer-group"></i> Group By</div>
            <div class="cc-filter-btn"><i class="fas fa-sort"></i> Sort</div>
            <div class="cc-view-toggles">
                <button class="cc-view-btn active"><i class="fas fa-list"></i></button>
                <button class="cc-view-btn"><i class="fas fa-th-large"></i></button>
                <button class="cc-view-btn"><i class="fas fa-th"></i></button>
            </div>
        </div>

        {{-- ══ ROW 4: TASK TABLE ══ --}}
        <div class="cc-table-wrap cc-anim cc-d5" style="display:block;min-height:50px;">
            <table class="cc-tbl">
                <thead>
                    <tr>
                        <th style="width:30px;">#</th>
                        <th style="width:24px;"></th>
                        <th>TASK</th>
                        <th>CATEGORY</th>
                        <th>CLIENT</th>
                        <th>PRIORITY</th>
                        <th>ASSIGNED TO</th>
                        <th class="sortable">DUE DATE <i class="fas fa-sort-up"></i></th>
                        <th>STATUS</th>
                        <th>COUNTDOWN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $ti => $t)
                    <tr>
                        <td class="cc-t-num">{{ $ti + 1 }}</td>
                        <td class="cc-t-expand"><i class="fas fa-chevron-right"></i></td>
                        <td class="cc-t-task">
                            <span class="cc-t-task-title">{{ $t['title'] }}</span>
                            <span class="cc-t-task-desc">{{ $t['desc'] }}</span>
                        </td>
                        <td><span class="cc-badge" style="background:{{ $t['cat_color'] }};color:#fff;">{{ $t['category'] }}</span></td>
                        <td class="cc-t-client">{{ $t['client'] }}</td>
                        <td><span class="cc-t-pri" style="color:{{ $t['pri_color'] }};"><i class="fas fa-flag"></i> {{ $t['priority'] }}</span></td>
                        <td>
                            <div class="cc-t-assigned">
                                <div class="cc-t-av" style="background:{{ $t['av_color'] }};">{{ $t['initials'] }}</div>
                                <span class="cc-t-av-name">{{ $t['assigned'] }}</span>
                            </div>
                        </td>
                        <td class="cc-t-due">{{ $t['due'] }}</td>
                        <td><span class="cc-badge" style="background:{{ $t['st_color'] }}30;color:{{ $t['st_color'] }};">{{ $t['status'] }}</span></td>
                        <td><span class="cc-t-cd" style="background:{{ $t['cd_color'] }}30;color:{{ $t['cd_color'] }};">{{ $t['countdown'] }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ══ ROW 5: CALENDAR + ACTIVITY FEED ══ --}}
        <div class="cc-mid cc-anim cc-d5">
            {{-- Calendar Panel --}}
            <div class="cc-panel">
                <div class="cc-cal-head">
                    <span class="cc-cal-month">MAY 2026 <i class="fas fa-chevron-down"></i></span>
                    <div class="cc-cal-nav">
                        <button><i class="fas fa-chevron-left"></i></button>
                        <button><i class="fas fa-chevron-right"></i></button>
                        <button class="cc-cal-today">Today</button>
                    </div>
                </div>
                <div class="cc-cal-grid">
                    <div class="cc-cal-dow">MON</div><div class="cc-cal-dow">TUE</div><div class="cc-cal-dow">WED</div><div class="cc-cal-dow">THU</div><div class="cc-cal-dow">FRI</div><div class="cc-cal-dow">SAT</div><div class="cc-cal-dow">SUN</div>
                    @foreach($calendarWeeks as $week)
                        @foreach($week as $day)
                        <div class="cc-cal-day {{ !empty($day['o']) ? 'other' : '' }} {{ !empty($day['today']) ? 'today' : '' }}">
                            <span>{{ $day['d'] }}</span>
                            <div class="cc-cal-dots">
                                @if(!empty($day['dots']))
                                    @foreach($day['dots'] as $dotColor)
                                    <i style="background:{{ $dotColor }};"></i>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @endforeach
                </div>
                <div class="cc-cal-legend">
                    <div class="cc-cal-legend-item"><span class="cc-cal-legend-dot" style="background:#3b82f6;"></span> SARS</div>
                    <div class="cc-cal-legend-item"><span class="cc-cal-legend-dot" style="background:#10b981;"></span> CIPC</div>
                    <div class="cc-cal-legend-item"><span class="cc-cal-legend-dot" style="background:#f59e0b;"></span> COIDA</div>
                    <div class="cc-cal-legend-item"><span class="cc-cal-legend-dot" style="background:#06b6d4;"></span> Compliance</div>
                    <div class="cc-cal-legend-item"><span class="cc-cal-legend-dot" style="background:#8b5cf6;"></span> Events</div>
                    <div class="cc-cal-legend-item"><span class="cc-cal-legend-dot" style="background:#94a3b8;"></span> Other</div>
                </div>
            </div>

            {{-- Activity Feed --}}
            <div class="cc-bot-panel">
                <div class="cc-bot-head">
                    <span class="cc-bot-title">Recent Activity Feed</span>
                    <a href="#" class="cc-bot-link">View all</a>
                </div>
                <div class="cc-bot-body">
                    @foreach($activityFeed as $act)
                    <div class="cc-act-item">
                        <span class="cc-act-time">{{ $act['time'] }}</span>
                        <div class="cc-act-icon" style="background:{{ $act['color'] }}20;color:{{ $act['color'] }};"><i class="fas {{ $act['icon'] }}"></i></div>
                        <div class="cc-act-text">
                            <span>{{ $act['text'] }}</span>
                            <span class="cc-act-sub">{{ $act['sub'] }}</span>
                        </div>
                        <span class="cc-act-badge" style="background:{{ $act['color'] }};color:#fff;">{{ $act['badge'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Team Workload --}}
            <div class="cc-bot-panel">
                <div class="cc-bot-head">
                    <span class="cc-bot-title">Team Workload</span>
                    <span class="cc-bot-dd">This Week <i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="cc-bot-body">
                    @foreach($teamMembers as $tm)
                    @php
                        $barColor = $tm['workload'] >= 85 ? '#ef4444' : ($tm['workload'] >= 65 ? '#f59e0b' : ($tm['workload'] >= 50 ? '#3b82f6' : '#10b981'));
                    @endphp
                    <div class="cc-wl-item">
                        <div class="cc-wl-av" style="background:{{ $tm['color'] }};">{{ $tm['initials'] }}</div>
                        <span class="cc-wl-name">{{ $tm['name'] }}</span>
                        <div class="cc-wl-bar"><div class="cc-wl-fill" style="width:{{ $tm['workload'] }}%;background:{{ $barColor }};"></div></div>
                        <span class="cc-wl-pct">{{ $tm['workload'] }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ══ ROW 6: BOTTOM PANELS ══ --}}
        <div class="cc-bottom cc-anim cc-d6">

            {{-- Compliance Radar --}}
            <div class="cc-bot-panel">
                <div class="cc-bot-head">
                    <span class="cc-bot-title">Compliance Radar</span>
                    <span class="cc-bot-dd">As at today <i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="cc-bot-body">
                    <div class="cc-radar-visual">
                        <div class="cc-radar-ring">
                            <svg viewBox="0 0 100 100" style="width:100%;height:100%;transform:rotate(-90deg);">
                                @php
                                $radarAngles = [96, 88, 79, 94, 90];
                                $radarColors = ['#3b82f6','#10b981','#f59e0b','#6366f1','#22c55e'];
                                $radarRadius = [44, 38, 32, 26, 20];
                                @endphp
                                @for($ri = 0; $ri < 5; $ri++)
                                <circle cx="50" cy="50" r="{{ $radarRadius[$ri] }}" fill="none" stroke="{{ $radarColors[$ri] }}22" stroke-width="5"/>
                                <circle cx="50" cy="50" r="{{ $radarRadius[$ri] }}" fill="none" stroke="{{ $radarColors[$ri] }}" stroke-width="5" stroke-dasharray="{{ ($radarAngles[$ri]/100) * 2 * 3.14159 * $radarRadius[$ri] }} {{ 2 * 3.14159 * $radarRadius[$ri] }}" stroke-linecap="round"/>
                                @endfor
                            </svg>
                            <div class="cc-radar-shield"><i class="fas fa-shield-alt"></i></div>
                        </div>
                        <div class="cc-radar-list">
                            @foreach($complianceRadar as $cr)
                            <div class="cc-radar-item">
                                <span class="cc-radar-dot" style="background:{{ $cr['color'] }};"></span>
                                <span class="cc-radar-name">{{ $cr['name'] }}</span>
                                <span class="cc-radar-score">{{ $cr['score'] }}%</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tasks by Category (Donut) --}}
            <div class="cc-bot-panel">
                <div class="cc-bot-head">
                    <span class="cc-bot-title">Tasks by Category</span>
                    <span class="cc-bot-dd">This Month <i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="cc-bot-body">
                    <div class="cc-donut-wrap">
                        <div class="cc-donut-ring">
                            <div class="cc-donut-hole">
                                <span class="cc-dn-num">1 248</span>
                                <span class="cc-dn-lbl">Total Tasks</span>
                            </div>
                        </div>
                        <div class="cc-donut-legend">
                            @foreach($categoryBreakdown as $cb)
                            <div class="cc-donut-legend-item">
                                <span class="cc-dl-dot" style="background:{{ $cb['color'] }};"></span>
                                <span class="cc-dl-name">{{ $cb['name'] }}</span>
                                <span class="cc-dl-val">{{ $cb['pct'] }}%</span>
                                <span class="cc-dl-count">({{ number_format($cb['count']) }})</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tasks by Status --}}
            <div class="cc-bot-panel">
                <div class="cc-bot-head">
                    <span class="cc-bot-title">Tasks by Status</span>
                    <span class="cc-bot-dd">This Month <i class="fas fa-chevron-down"></i></span>
                </div>
                <div class="cc-bot-body">
                    @foreach($statusBreakdown as $sb)
                    <div class="cc-st-item">
                        <span class="cc-st-dot" style="background:{{ $sb['color'] }};"></span>
                        <span class="cc-st-name">{{ $sb['name'] }}</span>
                        <div class="cc-st-bar"><div class="cc-st-fill" style="width:{{ $sb['pct'] }}%;background:{{ $sb['color'] }};"></div></div>
                        <span class="cc-st-count">{{ number_format($sb['count']) }}</span>
                        <span class="cc-st-pct">{{ $sb['pct'] }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

    </main>
</div>

{{-- SLIDE-OUT DRAWER --}}
<div class="cc-drawer-overlay" id="drawerOverlay"></div>
<div class="cc-drawer" id="drawer">
    <div class="cc-drawer-head">
        <span class="cc-drawer-cat-dot" id="drawerDot"></span>
        <div class="cc-drawer-title">
            <h3 id="drawerName"></h3>
            <span id="drawerSub"></span>
        </div>
        <div class="cc-drawer-close" id="drawerClose"><i class="fas fa-times"></i></div>
    </div>
    <div class="cc-drawer-body" id="drawerBody"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggleBtns = document.querySelectorAll('.cc-toggle-btn');
    toggleBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            btn.parentNode.querySelectorAll('.cc-toggle-btn').forEach(function(b) { b.classList.remove('active'); });
            btn.classList.add('active');
        });
    });

    var viewBtns = document.querySelectorAll('.cc-view-btn');
    viewBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            viewBtns.forEach(function(b) { b.classList.remove('active'); });
            btn.classList.add('active');
        });
    });

    var expandBtns = document.querySelectorAll('.cc-t-expand');
    expandBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var icon = btn.querySelector('i');
            if (icon.classList.contains('fa-chevron-right')) {
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-down');
            } else {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-right');
            }
        });
    });

    var team = [
        {name:'Krish Naidoo',initials:'KN',color:'#ef4444'},
        {name:'Shenell Govender',initials:'SG',color:'#8b5cf6'},
        {name:'Pravesh Maharaj',initials:'PM',color:'#f59e0b'},
        {name:'Vanessa Naidoo',initials:'VN',color:'#3b82f6'},
        {name:'Admin User',initials:'AU',color:'#6b7280'}
    ];
    var statuses = [
        {name:'To Do',color:'#6b7280'},
        {name:'In Progress',color:'#3b82f6'},
        {name:'Confirmed',color:'#10b981'},
        {name:'Completed',color:'#10b981'},
        {name:'Overdue',color:'#ef4444'}
    ];

    var drawerData = {
        'SARS EMP201':{cat:'SARS',color:'#3b82f6',tasks:[
            {title:'Submit EMP201 Return',desc:'EMP201 for April 2026 - Monthly PAYE reconciliation',client:'NexCore Africa (Pty) Ltd',assigned:'Krish Naidoo',initials:'KN',avColor:'#ef4444',due:'23 May 2026',status:'In Progress',stColor:'#3b82f6',priority:'Critical',priColor:'#ef4444'},
            {title:'Submit EMP201 Return',desc:'EMP201 for April 2026 - Monthly PAYE reconciliation',client:'NexCore Holdings (Pty) Ltd',assigned:'Shenell Govender',initials:'SG',avColor:'#8b5cf6',due:'23 May 2026',status:'To Do',stColor:'#6b7280',priority:'High',priColor:'#f97316'}
        ]},
        'PAYE Submission':{cat:'Payroll',color:'#6366f1',tasks:[
            {title:'PAYE Monthly Submission',desc:'Submit PAYE for May 2026',client:'NexCore Africa (Pty) Ltd',assigned:'Krish Naidoo',initials:'KN',avColor:'#ef4444',due:'21 May 2026',status:'In Progress',stColor:'#3b82f6',priority:'High',priColor:'#f97316'}
        ]},
        'Client Meeting':{cat:'Events',color:'#8b5cf6',tasks:[
            {title:'Client Review Meeting',desc:'Q2 Review & Planning session',client:'Saint Cricket (Pty) Ltd',assigned:'Shenell Govender',initials:'SG',avColor:'#8b5cf6',due:'23 May 2026',status:'Confirmed',stColor:'#10b981',priority:'Low',priColor:'#6b7280'},
            {title:'Client Onboarding Meeting',desc:'New client introduction and setup',client:'BluePeak Logistics (Pty) Ltd',assigned:'Krish Naidoo',initials:'KN',avColor:'#ef4444',due:'21 May 2026',status:'To Do',stColor:'#6b7280',priority:'Medium',priColor:'#eab308'}
        ]},
        'CIPC Annual Returns':{cat:'CIPC',color:'#10b981',tasks:[
            {title:'CIPC Annual Return Filing',desc:'Annual return for NexCore Holdings',client:'NexCore Holdings (Pty) Ltd',assigned:'Shenell Govender',initials:'SG',avColor:'#8b5cf6',due:'27 May 2026',status:'To Do',stColor:'#6b7280',priority:'High',priColor:'#f97316'},
            {title:'CIPC Annual Return Filing',desc:'Annual return for NexCore Africa',client:'NexCore Africa (Pty) Ltd',assigned:'Pravesh Maharaj',initials:'PM',avColor:'#f59e0b',due:'27 May 2026',status:'To Do',stColor:'#6b7280',priority:'High',priColor:'#f97316'},
            {title:'CIPC Annual Return Filing',desc:'Annual return for ATP Services',client:'ATP Services (Pty) Ltd',assigned:'Vanessa Naidoo',initials:'VN',avColor:'#3b82f6',due:'28 May 2026',status:'To Do',stColor:'#6b7280',priority:'Medium',priColor:'#eab308'}
        ]},
        'COIDA ROE':{cat:'COIDA',color:'#f59e0b',tasks:[
            {title:'COIDA Return of Earnings',desc:'ROE for FY2025',client:'NexCore Africa (Pty) Ltd',assigned:'Pravesh Maharaj',initials:'PM',avColor:'#f59e0b',due:'25 May 2026',status:'To Do',stColor:'#6b7280',priority:'High',priColor:'#f97316'}
        ]},
        'ATP Birthday':{cat:'Birthdays',color:'#ec4899',tasks:[
            {title:'ATP Services - Gavin',desc:'Birthday - Send Greeting',client:'ATP Services (Pty) Ltd',assigned:'Admin User',initials:'AU',avColor:'#6b7280',due:'22 May 2026',status:'To Do',stColor:'#6b7280',priority:'Low',priColor:'#6b7280'}
        ]},
        'VAT201 Submission':{cat:'SARS',color:'#f59e0b',tasks:[
            {title:'VAT201 Submission',desc:'VAT return for May 2026',client:'NexCore Africa (Pty) Ltd',assigned:'Krish Naidoo',initials:'KN',avColor:'#ef4444',due:'23 May 2026',status:'To Do',stColor:'#6b7280',priority:'Critical',priColor:'#ef4444'},
            {title:'VAT201 Submission',desc:'VAT return for May 2026',client:'ICCED (Pty) Ltd',assigned:'Pravesh Maharaj',initials:'PM',avColor:'#f59e0b',due:'23 May 2026',status:'To Do',stColor:'#6b7280',priority:'High',priColor:'#f97316'}
        ]},
        'Unions/MIBCO':{cat:'Events',color:'#8b5cf6',tasks:[
            {title:'Unions/MIBCO Submission',desc:'Monthly union contribution report',client:'NexCore Africa (Pty) Ltd',assigned:'Vanessa Naidoo',initials:'VN',avColor:'#3b82f6',due:'23 May 2026',status:'To Do',stColor:'#6b7280',priority:'Medium',priColor:'#eab308'}
        ]},
        'COIDA Assessment':{cat:'COIDA',color:'#f59e0b',tasks:[
            {title:'COIDA Assessment Review',desc:'Review annual assessment letter',client:'NexCore Africa (Pty) Ltd',assigned:'Pravesh Maharaj',initials:'PM',avColor:'#f59e0b',due:'24 May 2026',status:'In Progress',stColor:'#3b82f6',priority:'High',priColor:'#f97316'},
            {title:'COIDA Assessment Review',desc:'Review annual assessment letter',client:'NexCore Holdings (Pty) Ltd',assigned:'Pravesh Maharaj',initials:'PM',avColor:'#f59e0b',due:'25 May 2026',status:'To Do',stColor:'#6b7280',priority:'Medium',priColor:'#eab308'}
        ]},
        'BEE Certificate Expiry':{cat:'Compliance',color:'#06b6d4',tasks:[
            {title:'BEE Certificate Renewal',desc:'Certificate expires 30 June 2026',client:'NexCore Africa (Pty) Ltd',assigned:'Vanessa Naidoo',initials:'VN',avColor:'#3b82f6',due:'30 Jun 2026',status:'To Do',stColor:'#6b7280',priority:'Medium',priColor:'#eab308'}
        ]},
        'Annual Financials':{cat:'Events',color:'#8b5cf6',tasks:[
            {title:'Annual Financial Statements',desc:'Prepare FY2025 annual financials',client:'NexCore Africa (Pty) Ltd',assigned:'Krish Naidoo',initials:'KN',avColor:'#ef4444',due:'28 May 2026',status:'In Progress',stColor:'#3b82f6',priority:'High',priColor:'#f97316'},
            {title:'Annual Financial Statements',desc:'Prepare FY2025 annual financials',client:'ICCED (Pty) Ltd',assigned:'Shenell Govender',initials:'SG',avColor:'#8b5cf6',due:'30 May 2026',status:'To Do',stColor:'#6b7280',priority:'Medium',priColor:'#eab308'}
        ]},
        'Board Meeting':{cat:'Events',color:'#94a3b8',tasks:[
            {title:'Board Meeting Preparation',desc:'Q2 board pack and agenda',client:'NexCore Holdings (Pty) Ltd',assigned:'Krish Naidoo',initials:'KN',avColor:'#ef4444',due:'1 Jun 2026',status:'To Do',stColor:'#6b7280',priority:'Low',priColor:'#6b7280'}
        ]}
    };

    var overlay = document.getElementById('drawerOverlay');
    var drawer = document.getElementById('drawer');
    var drawerBody = document.getElementById('drawerBody');

    function openDrawer(name, dateLabel) {
        var data = drawerData[name];
        if (!data) return;
        document.getElementById('drawerDot').style.background = data.color;
        document.getElementById('drawerName').textContent = name + ' - ' + data.tasks.length + (data.tasks.length === 1 ? ' task' : ' tasks');
        document.getElementById('drawerSub').textContent = data.cat + ' | ' + (dateLabel || '');

        var html = '';
        for (var i = 0; i < data.tasks.length; i++) {
            var t = data.tasks[i];
            html += '<div class="cc-dtask">';
            html += '<div class="cc-dtask-top">';
            html += '<span class="cc-dtask-num">' + (i+1) + '</span>';
            html += '<div class="cc-dtask-info"><div class="cc-dtask-name">' + t.title + '</div><div class="cc-dtask-desc">' + t.desc + '</div></div>';
            html += '<span class="cc-dtask-status" style="background:' + t.stColor + '30;color:' + t.stColor + ';">' + t.status + '</span>';
            html += '</div>';
            html += '<div class="cc-dtask-meta">';
            html += '<div class="cc-dtask-meta-item"><i class="fas fa-building"></i> ' + t.client + '</div>';
            html += '<div class="cc-dtask-meta-item"><div class="cc-dtask-av" style="background:' + t.avColor + ';">' + t.initials + '</div> ' + t.assigned + '</div>';
            html += '<div class="cc-dtask-meta-item"><i class="fas fa-calendar"></i> ' + t.due + '</div>';
            html += '<div class="cc-dtask-meta-item"><i class="fas fa-flag" style="color:' + t.priColor + ';"></i> ' + t.priority + '</div>';
            html += '</div>';
            html += '<div class="cc-dtask-actions">';
            html += '<button class="cc-dtask-btn primary" onclick="event.stopPropagation()"><i class="fas fa-external-link-alt"></i> Open</button>';
            html += '<button class="cc-dtask-btn" data-dd="assign-' + i + '" onclick="toggleDD(this,event)"><i class="fas fa-user-plus"></i> Assign';
            html += '<div class="cc-dd-menu" id="assign-' + i + '">';
            for (var j = 0; j < team.length; j++) {
                html += '<div class="cc-dd-opt" onclick="pickAssign(this,event)"><div class="cc-dd-dot" style="background:' + team[j].color + ';"></div> ' + team[j].name + '</div>';
            }
            html += '</div></button>';
            html += '<button class="cc-dtask-btn" data-msg="msg-' + i + '" onclick="toggleMsg(this,event)"><i class="fas fa-comment"></i> Message</button>';
            html += '<button class="cc-dtask-btn" data-dd="status-' + i + '" onclick="toggleDD(this,event)"><i class="fas fa-circle-notch"></i> Status';
            html += '<div class="cc-dd-menu" id="status-' + i + '">';
            for (var k = 0; k < statuses.length; k++) {
                html += '<div class="cc-dd-opt" onclick="pickStatus(this,event)"><div class="cc-dd-dot" style="background:' + statuses[k].color + ';"></div> ' + statuses[k].name + '</div>';
            }
            html += '</div></button>';
            html += '</div>';
            html += '<div class="cc-msg-box" id="msg-' + i + '"><textarea class="cc-msg-input" rows="2" placeholder="Type a message..."></textarea><button class="cc-msg-send" onclick="sendMsg(this,event)">Send Message</button></div>';
            html += '</div>';
        }
        drawerBody.innerHTML = html;
        overlay.classList.add('open');
        drawer.classList.add('open');
    }

    function closeDrawer() {
        overlay.classList.remove('open');
        drawer.classList.remove('open');
    }

    overlay.addEventListener('click', closeDrawer);
    document.getElementById('drawerClose').addEventListener('click', closeDrawer);
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeDrawer(); });

    var tlItems = document.querySelectorAll('.cc-tl-item');
    tlItems.forEach(function(item) {
        item.style.cursor = 'pointer';
        item.addEventListener('click', function() {
            var nameEl = item.querySelector('.cc-tl-name');
            var name = nameEl ? nameEl.textContent.trim() : '';
            var colEl = item.closest('.cc-tl-col');
            var dateEl = colEl ? colEl.querySelector('.cc-tl-date') : null;
            var lblEl = colEl ? colEl.querySelector('.cc-tl-lbl') : null;
            var dateLabel = '';
            if (lblEl) dateLabel += lblEl.textContent.trim() + ' - ';
            if (dateEl) dateLabel += dateEl.textContent.trim();
            openDrawer(name, dateLabel);
        });
    });

    window.toggleDD = function(btn, e) {
        e.stopPropagation();
        var ddId = btn.getAttribute('data-dd');
        var dd = document.getElementById(ddId);
        document.querySelectorAll('.cc-dd-menu.show').forEach(function(m) { if (m !== dd) m.classList.remove('show'); });
        dd.classList.toggle('show');
    };
    window.toggleMsg = function(btn, e) {
        e.stopPropagation();
        var msgId = btn.getAttribute('data-msg');
        var box = document.getElementById(msgId);
        box.classList.toggle('show');
        if (box.classList.contains('show')) box.querySelector('textarea').focus();
    };
    window.pickAssign = function(opt, e) {
        e.stopPropagation();
        var name = opt.textContent.trim();
        var btn = opt.closest('.cc-dtask-btn');
        var taskCard = opt.closest('.cc-dtask');
        var metaItems = taskCard.querySelectorAll('.cc-dtask-meta-item');
        if (metaItems[1]) {
            var avEl = metaItems[1].querySelector('.cc-dtask-av');
            var dotEl = opt.querySelector('.cc-dd-dot');
            if (avEl && dotEl) avEl.style.background = dotEl.style.background;
            var nameParts = name.split(' ');
            if (avEl) avEl.textContent = nameParts[0][0] + (nameParts[1] ? nameParts[1][0] : '');
            metaItems[1].lastChild.textContent = ' ' + name;
        }
        opt.closest('.cc-dd-menu').classList.remove('show');
    };
    window.pickStatus = function(opt, e) {
        e.stopPropagation();
        var name = opt.textContent.trim();
        var dotEl = opt.querySelector('.cc-dd-dot');
        var color = dotEl ? dotEl.style.background : '#6b7280';
        var taskCard = opt.closest('.cc-dtask');
        var badge = taskCard.querySelector('.cc-dtask-status');
        if (badge) { badge.textContent = name; badge.style.background = color + '30'; badge.style.color = color; }
        opt.closest('.cc-dd-menu').classList.remove('show');
    };
    window.sendMsg = function(btn, e) {
        e.stopPropagation();
        var box = btn.closest('.cc-msg-box');
        var textarea = box.querySelector('textarea');
        if (textarea.value.trim()) {
            textarea.value = '';
            box.classList.remove('show');
            var taskCard = btn.closest('.cc-dtask');
            var flash = document.createElement('div');
            flash.style.cssText = 'padding:6px 12px;background:rgba(6,182,212,0.1);border:1px solid rgba(6,182,212,0.3);border-radius:6px;font-size:11px;color:#06b6d4;margin-top:6px;';
            flash.innerHTML = '<i class="fas fa-check"></i> Message sent successfully';
            taskCard.appendChild(flash);
            setTimeout(function() { flash.remove(); }, 3000);
        }
    };

    document.addEventListener('click', function() {
        document.querySelectorAll('.cc-dd-menu.show').forEach(function(m) { m.classList.remove('show'); });
    });
});
</script>
</body>
</html>
