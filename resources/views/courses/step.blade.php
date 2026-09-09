@extends('layouts.app')
@section('title', $step->title . ' — CodeMaster')

@section('head')
<style>
    .st-page{background:var(--bg);color:var(--text);overflow-x:clip}
    .st-hero{position:relative;overflow:hidden;isolation:isolate;border-bottom:1px solid var(--border)}
    .st-hero-bg{position:absolute;inset:0;z-index:0;pointer-events:none}
    .st-hero-grid{position:absolute;inset:0;opacity:.5;background-image:linear-gradient(var(--border) 1px,transparent 1px),linear-gradient(90deg,var(--border) 1px,transparent 1px);background-size:44px 44px;mask-image:radial-gradient(ellipse 75% 90% at 20% 10%,black 20%,transparent 70%);-webkit-mask-image:radial-gradient(ellipse 75% 90% at 20% 10%,black 20%,transparent 70%)}
    .st-orb{position:absolute;border-radius:50%;filter:blur(90px)}
    .st-orb-1{width:420px;height:420px;background:var(--accent);opacity:.13;top:-160px;left:-100px}
    .st-orb-2{width:340px;height:340px;background:#8b5cf6;opacity:.10;bottom:-180px;right:-60px}
    .st-inner{position:relative;z-index:1;max-width:1280px;margin:0 auto;padding:14px clamp(16px,4vw,32px) 12px}
    .st-topbar{display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap}
    .st-back{display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:700;color:var(--text-muted);text-decoration:none;padding:7px 12px;border:1px solid var(--border);border-radius:10px;background:var(--card)}
    .st-back:hover{color:var(--accent);border-color:var(--accent)}
    .st-crumb{font-size:12px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;min-width:0}
    .st-crumb a{color:var(--text-muted);text-decoration:none}
    .st-crumb a:hover{color:var(--accent)}
    .st-crumb .sep{font-size:9px;opacity:.6;margin:0 6px}
    .st-crumb .cur{color:var(--text);font-weight:600}
    .st-hact{margin-left:auto;display:flex;gap:7px;align-items:center;flex-shrink:0}
    .st-iconbtn{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:10px;border:1px solid var(--border);background:var(--card);color:var(--text-secondary);cursor:pointer;font-size:12px;text-decoration:none}
    .st-iconbtn:hover{color:var(--accent);border-color:var(--accent)}
    .st-titlerow{display:flex;align-items:center;gap:10px}
    .st-stepnum{font-size:10.5px;font-weight:800;letter-spacing:.06em;background:linear-gradient(135deg,var(--accent),#8b5cf6);color:#fff;padding:4px 10px;border-radius:99px;white-space:nowrap;flex-shrink:0}
    .st-chip{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;padding:4px 10px;border-radius:99px;border:1px solid var(--border);background:var(--card);color:var(--text-secondary);white-space:nowrap;flex-shrink:0}
    .st-chip.accent{background:var(--accent-glow);border-color:var(--accent-glow-strong);color:var(--accent)}
    .st-chip.green{background:rgba(34,197,94,.12);border-color:rgba(34,197,94,.35);color:#22c55e}
    .st-title{font-size:19px;font-weight:800;letter-spacing:-.01em;margin:0;flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .st-thinbar{height:3px;background:var(--bg-secondary)}
    .st-thinbar>span{display:block;height:100%;background:linear-gradient(90deg,var(--accent),#8b5cf6)}
    .st-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 22px;border-radius:12px;font-size:14px;font-weight:700;text-decoration:none;border:1px solid transparent;cursor:pointer;transition:transform .15s,box-shadow .15s,border-color .15s,color .15s}
    .st-btn:active{transform:scale(.97)}
    .st-btn-primary{background:linear-gradient(135deg,var(--accent),var(--accent-hover,var(--accent-2)));color:#fff;box-shadow:0 6px 22px var(--accent-glow-strong)}
    .st-btn-primary:hover{transform:translateY(-2px)}
    .st-btn-ghost{background:var(--card);border-color:var(--border);color:var(--text)}
    .st-btn-ghost:hover{border-color:var(--accent);color:var(--accent)}
    .st-btn-done{background:rgba(34,197,94,.14);border-color:rgba(34,197,94,.35);color:#22c55e;cursor:default}
    .st-btn-sm{padding:7px 13px;font-size:12px}
    .st-btn[disabled]{opacity:.6;cursor:wait}
    .st-body{max-width:1280px;margin:0 auto;padding:16px clamp(16px,4vw,32px) 90px;display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:20px;align-items:start}
    .st-tabs{position:sticky;top:64px;z-index:40;display:flex;gap:8px;align-items:center;padding:7px;background:color-mix(in srgb,var(--card) 88%,transparent);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border:1px solid var(--border);border-radius:13px;margin-bottom:12px;box-shadow:0 8px 28px rgba(0,0,0,.18)}
    .st-tabs-row{display:flex;gap:6px;flex:1;min-width:0;overflow-x:auto}
    .st-navfold{flex-shrink:0;width:36px;height:36px;border-radius:10px;border:1px solid var(--border);background:var(--bg-secondary);color:var(--text-muted);cursor:pointer;font-size:12px}
    .st-navfold:hover{color:var(--accent);border-color:var(--accent)}
    .st-navfold i{transition:transform .2s}
    .st-tabs--mini{width:max-content;max-width:100%;padding:5px;gap:0}
    .st-tab{display:inline-flex;align-items:center;gap:8px;padding:8px 13px;border-radius:10px;font-size:13px;font-weight:700;color:var(--text-muted);background:none;border:none;cursor:pointer;white-space:nowrap}
    .st-tab:hover{color:var(--text);background:var(--bg-secondary)}
    .st-tab.active{color:var(--accent);background:var(--accent-glow)}
    .st-tab .cnt{font-size:10.5px;font-weight:800;background:var(--bg-secondary);border:1px solid var(--border);border-radius:99px;padding:2px 8px}
    .st-tab.active .cnt{background:var(--accent-glow);border-color:var(--accent-glow-strong);color:var(--accent)}
    .st-tab .cnt.done{background:rgba(34,197,94,.14);border-color:rgba(34,197,94,.35);color:#22c55e}
    .st-panel{display:flex;flex-direction:column;gap:14px}
    .st-card{background:var(--card);border:1px solid var(--border);border-radius:18px;overflow:hidden}
    .st-article{padding:26px clamp(18px,3vw,32px);font-size:15px;line-height:1.78}
    .st-article>:first-child{margin-top:0}.st-article>:last-child{margin-bottom:0}
    .st-article h1,.st-article h2{font-size:1.35em;font-weight:800;margin:1.4em 0 .6em;line-height:1.3}
    .st-article h1:first-child,.st-article h2:first-child{margin-top:0}
    .st-article h2{padding-bottom:.35em;border-bottom:1px solid var(--border)}
    .st-article h3,.st-article h4{font-size:1.1em;font-weight:750;margin:1.3em 0 .5em}
    .st-article p{margin:.8em 0}
    .st-article ul,.st-article ol{margin:.8em 0;padding-left:1.5em}
    .st-article li{margin:.35em 0}
    .st-article li::marker{color:var(--accent);font-weight:700}
    .st-article a{color:var(--accent);text-decoration:underline;text-underline-offset:3px}
    .st-article blockquote{margin:1em 0;padding:12px 16px;border-left:3px solid var(--accent);background:var(--accent-glow);border-radius:0 12px 12px 0}
    .st-article pre{margin:1em 0;border-radius:12px;overflow:auto;background:#0d1117;border:1px solid var(--border);position:relative}
    .st-article pre code{display:block;padding:16px;font-family:ui-monospace,'Cascadia Code',Consolas,monospace;font-size:13px;line-height:1.65;color:#e6edf3;background:transparent}
    .st-article :not(pre)>code{font-family:ui-monospace,Consolas,monospace;font-size:.85em;background:var(--accent-glow);color:var(--accent);border:1px solid var(--accent-glow-strong);padding:1px 7px;border-radius:7px;white-space:nowrap}
    .st-article table{width:100%;border-collapse:collapse;margin:1em 0;font-size:13.5px;display:block;overflow-x:auto}
    .st-article th,.st-article td{border:1px solid var(--border);padding:9px 12px;text-align:left}
    .st-article th{background:var(--bg-secondary);font-weight:750}
    .st-article img{max-width:100%;border-radius:12px;border:1px solid var(--border)}
    .st-article hr{border:none;border-top:1px solid var(--border);margin:1.5em 0}
    .st-codewrap{position:relative}
    .st-copy{position:absolute;top:8px;right:8px;font-size:11px;font-weight:700;padding:6px 10px;border-radius:8px;background:rgba(255,255,255,.08);color:#c9d1d9;border:1px solid rgba(255,255,255,.15);cursor:pointer}
    .st-copy:hover{background:rgba(255,255,255,.16)}
    .st-sec-title{padding:20px 22px 0;margin:0;font-size:16px;font-weight:800}
    .st-sec-sub{padding:4px 22px 0;margin:0;font-size:12.5px;color:var(--text-muted)}
    .st-listtools{display:flex;justify-content:flex-end;gap:2px;padding:10px 16px 0}
    .st-tool{font-size:11.5px;font-weight:700;color:var(--accent);background:none;border:none;cursor:pointer;padding:6px 8px;white-space:nowrap;font-family:inherit}
    .st-tool:hover{text-decoration:underline}
    .st-sec-head{width:100%;display:flex;align-items:center;gap:10px;padding:16px 20px;background:none;border:none;cursor:pointer;font-size:14px;font-weight:800;color:var(--text);text-align:left;font-family:inherit}
    .st-sec-head:hover{color:var(--accent)}
    .st-fold-chev{margin-left:auto;font-size:11px;color:var(--text-muted);transition:transform .2s;flex-shrink:0}
    .st-fold-chev.rot{transform:rotate(180deg);color:var(--accent)}
    .st-article--fold{padding-top:8px}
    .st-res-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;padding:16px 22px 22px}
    .st-res-card{display:flex;gap:10px;align-items:center;padding:12px 14px;background:var(--bg-secondary);border:1px solid var(--border);border-radius:12px;text-decoration:none}
    .st-res-card:hover{border-color:var(--accent);transform:translateY(-2px)}
    .st-res-ic{width:34px;height:34px;border-radius:10px;background:var(--accent-glow);color:var(--accent);display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .st-res-tx b{display:block;font-size:13px;color:var(--text)}
    .st-res-tx small{font-size:11px;color:var(--text-muted)}
    .st-acc{padding:16px;display:flex;flex-direction:column;gap:10px}
    .st-acc-item{background:var(--bg-secondary);border:1px solid var(--border);border-radius:14px;overflow:hidden}
    .st-acc-item[open]{border-color:var(--accent)}
    .st-acc-item summary{list-style:none;display:flex;align-items:center;gap:12px;padding:15px 18px;cursor:pointer;font-weight:700;font-size:14px}
    .st-acc-item summary::-webkit-details-marker{display:none}
    .st-acc-n{width:28px;height:28px;border-radius:9px;background:var(--accent-glow);color:var(--accent);font-size:12.5px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid var(--accent-glow-strong)}
    .st-acc-t{flex:1}
    .st-xp{font-size:10.5px;font-weight:800;color:var(--accent);background:var(--accent-glow);border:1px solid var(--accent-glow-strong);padding:3px 9px;border-radius:99px;white-space:nowrap}
    .st-acc-chev{font-size:11px;color:var(--text-muted)}
    .st-acc-item[open] .st-acc-chev{transform:rotate(180deg);color:var(--accent)}
    .st-acc-body{border-top:1px dashed var(--border);font-size:14px;line-height:1.7}
    .st-acc-links{display:flex;flex-wrap:wrap;gap:8px;padding:0 18px 16px}
    .st-acc-links a{font-size:12px;font-weight:600;color:var(--accent);text-decoration:none;background:var(--accent-glow);padding:6px 12px;border-radius:99px}
    .st-pbar{height:8px;border-radius:99px;background:var(--bg-secondary);border:1px solid var(--border);overflow:hidden;margin:16px 22px 0}
    .st-pbar>span{display:block;height:100%;background:linear-gradient(90deg,#22c55e,#16a34a);border-radius:99px}
    .st-qlist{display:flex;flex-direction:column;gap:12px;padding:16px}
    .st-q{background:var(--bg-secondary);border:1px solid var(--border);border-radius:14px;padding:18px}
    .st-q.is-passed{border-color:rgba(34,197,94,.4)}
    .st-q-head{width:100%;display:flex;align-items:center;gap:8px;flex-wrap:wrap;background:none;border:none;cursor:pointer;padding:0;text-align:left;font-family:inherit}
    .st-q-body{padding-top:12px}
    .st-q-n{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;color:var(--text-muted)}
    .st-q-type{font-size:11px;font-weight:700;color:var(--accent);background:var(--accent-glow);padding:4px 10px;border-radius:99px}
    .st-q-type i{margin-right:5px}
    .st-q-ok{font-size:11px;font-weight:700;color:#22c55e;background:rgba(34,197,94,.12);padding:4px 10px;border-radius:99px}
    .st-q-text{font-size:14.5px;font-weight:650;margin:0 0 4px;line-height:1.6}
    .st-q-hint{font-size:12px;color:var(--text-muted);margin:0 0 12px}
    .st-opts{display:flex;flex-direction:column;gap:8px;margin-bottom:12px}
    .st-opt{display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid var(--border);border-radius:12px;background:var(--card);cursor:pointer;font-size:13.5px;transition:border-color .15s,background .15s}
    .st-opt:hover{border-color:var(--accent)}
    .st-opt input{position:absolute;opacity:0;pointer-events:none}
    .st-opt-dot{width:20px;height:20px;border-radius:50%;border:2px solid var(--border);flex-shrink:0;position:relative}
    .st-opt-box{width:20px;height:20px;border-radius:7px;border:2px solid var(--border);flex-shrink:0;position:relative;font-size:11px;color:#fff;display:flex;align-items:center;justify-content:center}
    .st-opt.picked{border-color:var(--accent);background:var(--accent-glow)}
    .st-opt.picked .st-opt-dot{border-color:var(--accent)}
    .st-opt.picked .st-opt-dot::after{content:'';position:absolute;inset:3px;border-radius:50%;background:var(--accent)}
    .st-opt.picked .st-opt-box{background:var(--accent);border-color:var(--accent)}
    .st-opt.picked .st-opt-box::after{content:'✓'}
    .st-input{width:100%;box-sizing:border-box;padding:12px 14px;border-radius:12px;font-size:14px;background:var(--card);border:1px solid var(--border);color:var(--text);margin-bottom:12px}
    .st-input:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
    .st-tf{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px}
    .st-tf-card{display:flex;align-items:center;justify-content:center;gap:8px;padding:14px;border-radius:12px;border:1px solid var(--border);background:var(--card);cursor:pointer;font-size:13.5px}
    .st-tf-card input{position:absolute;opacity:0;pointer-events:none}
    .st-tf-card.picked{border-color:#22c55e;background:rgba(34,197,94,.1);color:#22c55e}
    .st-tf-card.st-tf-no.picked{border-color:#ef4444;background:rgba(239,68,68,.08);color:#ef4444}
    .st-match{display:flex;flex-direction:column;gap:8px;margin-bottom:12px}
    .st-match-row{display:grid;grid-template-columns:1fr 28px 1fr;gap:8px;align-items:center;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:10px 12px}
    .st-match-l{font-size:13px;font-weight:650}
    .st-match-arr{color:var(--accent);font-size:12px;text-align:center}
    .st-select{padding:10px 12px;border-radius:10px;font-size:12.5px;background:var(--bg-secondary);border:1px solid var(--border);color:var(--text);max-width:100%}
    .st-select:focus{outline:none;border-color:var(--accent)}
    .st-fb{margin-top:12px;padding:12px 14px;border-radius:12px;font-size:13px;font-weight:600}
    .st-fb.is-ok{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:#22c55e}
    .st-fb.is-err{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.3);color:#ef4444}
    .st-fb small{display:block;font-weight:500;margin-top:4px;opacity:.85}
    .st-exam{background:var(--bg-secondary);border:1px solid var(--border);border-left:3px solid #f59e0b;border-radius:14px;padding:18px}
    .st-exam .st-q-head{margin:0}
    .st-exam-type{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;padding:4px 10px;border-radius:99px;background:rgba(245,158,11,.12);color:#d9a406}
    .st-exam-diff{font-size:10.5px;font-weight:800;text-transform:uppercase;padding:4px 10px;border-radius:99px;background:var(--card);border:1px solid var(--border);color:var(--text-muted)}
    .st-exam-opts{margin:10px 0;padding:0;list-style:none;display:flex;flex-direction:column;gap:6px}
    .st-exam-opts li{background:var(--card);border:1px solid var(--border);border-radius:9px;padding:8px 12px;font-size:13.5px}
    .st-ans{margin-top:10px;font-size:13px}
    .st-ans summary{cursor:pointer;font-weight:700;color:var(--accent);display:inline-flex;align-items:center;gap:6px}
    .st-ans>div{margin-top:8px;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:12px 14px}
    .st-ans-exp{color:var(--text-secondary);margin:6px 0 0}
    .st-slides{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;padding:16px}
    .st-slide{background:var(--bg-secondary);border:1px solid var(--border);border-radius:14px;padding:18px}
    .st-slide>header{display:flex;gap:10px;align-items:center;margin-bottom:8px}
    .st-slide>header span{width:28px;height:28px;border-radius:9px;background:linear-gradient(135deg,var(--accent),#8b5cf6);color:#fff;font-size:12px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .st-slide h3{margin:0;font-size:14px;font-weight:750}
    .st-slide .st-article{padding:8px 0 0;font-size:13.5px}
    .st-empty{padding:40px 24px;text-align:center;color:var(--text-muted)}
    .st-empty i{font-size:30px;opacity:.35;display:block;margin-bottom:10px}
    .st-empty b{color:var(--text);display:block;margin-bottom:4px;font-size:15px}
    .st-empty p{font-size:13px;margin:0 0 14px}
    .st-next-list{display:flex;flex-direction:column;gap:10px}
    .st-next-card{display:flex;gap:12px;align-items:center;padding:14px 16px;background:var(--card);border:1px solid var(--border);border-radius:14px;text-decoration:none}
    .st-next-card:hover{transform:translateY(-2px);border-color:var(--accent)}
    .st-next-ic{width:38px;height:38px;border-radius:12px;background:var(--accent-glow);color:var(--accent);display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .st-next-card b{display:block;font-size:14px;color:var(--text)}
    .st-next-card small{font-size:12px;color:var(--text-muted)}
    .st-bottom-nav{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:4px}
    .st-side{position:sticky;top:88px;display:flex;flex-direction:column;gap:16px}
    .st-side .st-card{padding:20px 22px}
    .st-side h3{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--text-muted);margin:0 0 12px}
    .st-side h3 small{float:right;color:var(--accent)}
    .st-progress-top{display:flex;align-items:center;justify-content:space-between;font-size:12.5px;margin-bottom:7px}
    .st-progress-top b{color:var(--accent)}
    .st-progress{height:8px;border-radius:99px;background:var(--bg-secondary);border:1px solid var(--border);overflow:hidden}
    .st-progress>span{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,var(--accent),#8b5cf6)}
    .st-outline{display:flex;flex-direction:column;max-height:380px;overflow-y:auto;gap:2px}
    .st-outline a{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:10px;text-decoration:none;font-size:12.5px}
    .st-outline a:hover{background:var(--bg-secondary)}
    .st-outline a.cur{background:var(--accent-glow);box-shadow:inset 3px 0 0 var(--accent)}
    .st-o-check{width:22px;height:22px;border-radius:50%;border:1.5px dashed var(--border);font-size:10px;font-weight:700;color:var(--text-muted);display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .st-outline a.done .st-o-check{background:rgba(34,197,94,.15);border:solid 1.5px #22c55e;color:#22c55e}
    .st-outline a.done .st-o-t{color:var(--text-muted)}
    .st-o-t{flex:1;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .st-o-xp{font-size:10px;font-weight:800;color:var(--accent)}
    .st-tip{background:linear-gradient(160deg,var(--accent-glow),transparent 70%),var(--card)}
    .st-tip ul{margin:0;padding-left:1.1em;font-size:12.5px;color:var(--text-secondary);line-height:1.65}
    .st-mobilebar{display:none;position:fixed;left:0;right:0;bottom:0;z-index:70;padding:10px 14px calc(10px + env(safe-area-inset-bottom));gap:10px;background:color-mix(in srgb,var(--card) 90%,transparent);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border-top:1px solid var(--border)}
    .st-mobilebar .st-btn{flex:1}
    @media (max-width:1024px){.st-body{grid-template-columns:1fr}.st-side{position:static}.st-tabs{top:56px}}
    @media (max-width:640px){.st-tf{grid-template-columns:1fr}.st-match-row{grid-template-columns:1fr}.st-match-arr{transform:rotate(90deg)}.st-article{font-size:14px}.st-mobilebar{display:flex}.st-body{padding-bottom:110px}.st-bottom-nav{grid-template-columns:1fr}.st-title{font-size:16px}.st-crumb{display:none}}
</style>
@endsection

@section('content')
@php
    $typeLabels = [
        'one_correct' => ['Один ответ', 'fa-dot-circle', 'Выберите один правильный вариант'],
        'list_correct' => ['Несколько ответов', 'fa-check-square', 'Отметьте все правильные варианты'],
        'question_answer' => ['Открытый ответ', 'fa-keyboard', 'Впишите ответ текстом'],
        'true_false' => ['Верно / Неверно', 'fa-balance-scale', 'Оцените утверждение'],
        'matching' => ['Соответствие', 'fa-link', 'Сопоставьте пары из двух колонок'],
    ];
    $stepIdx = ($courseSteps ?? collect())->search(fn($s) => (int) $s->id === (int) $step->id);
    $stepIdx = $stepIdx === false ? null : $stepIdx + 1;
    $passedCount = count(array_intersect($testResults ?? [], $step->tests->pluck('id')->toArray()));
    $testsTotal = $step->tests->count();
    $hasLecture = !empty(trim(strip_tags((string) $step->description)));
    $hasVocab = $step->vocabularies->count() > 0;
    $hasTests = $testsTotal > 0;
    $hasExams = $step->exams->count() > 0;
    $hasSlides = $step->slides->count() > 0;
    $canAct = ($enrollment || $isOwner);
    $isDone = (bool) $stepProgress;
    $defaultTab = $hasLecture ? 'theory' : ($hasVocab ? 'concepts' : ($hasTests ? 'practice' : ($hasExams ? 'exam' : 'slides')));
    $tabsCount = (int)$hasLecture + (int)$hasVocab + (int)$hasTests + (int)$hasExams + (int)$hasSlides;
@endphp

<div class="st-page" x-data="{ activeTab: '{{ $defaultTab }}', tabName(t) { return { theory: 'Теория', concepts: 'Разбор', practice: 'Практика', exam: 'Экзамен', slides: 'Конспект' }[t] || ''; } }">
    {{-- ================= HERO ================= --}}
    <section class="st-hero">
        <div class="st-hero-bg"><div class="st-hero-grid"></div><div class="st-orb st-orb-1"></div><div class="st-orb st-orb-2"></div></div>
        <div class="st-inner">
            <div class="st-topbar">
                <a href="{{ route('courses.show', $course->id) }}" class="st-back" title="К содержанию курса"><i class="fas fa-arrow-left"></i><span>Курс</span></a>
                <span class="st-crumb"><a href="{{ route('courses.show', $course->id) }}">{{ mb_strimwidth($course->topic ?? $course->title, 0, 30, '…') }}</a><span class="sep">›</span><span class="cur">{{ mb_strimwidth($step->title, 0, 34, '…') }}</span></span>
                <div class="st-hact">
                    @if($isDone)
                        <span class="st-chip green"><i class="fas fa-check"></i>Пройден</span>
                    @elseif($canAct)
                        <button onclick="stComplete()" id="stCompleteBtn" class="st-btn st-btn-primary st-btn-sm"><i class="fas fa-flag-checkered"></i>Завершить</button>
                    @endif
                    @if($isOwner)
                        <button onclick="stGen('all', this)" class="st-iconbtn" title="AI: догенерировать"><i class="fas fa-wand-magic-sparkles"></i></button>
                    @endif
                    @if(!empty($prevStep))
                        <a href="{{ route('courses.step', [$course->id, $prevStep->id]) }}" class="st-iconbtn" title="{{ $prevStep->title }}"><i class="fas fa-chevron-left"></i></a>
                    @endif
                    @if(!empty($nextStep))
                        <a href="{{ route('courses.step', [$course->id, $nextStep->id]) }}" class="st-iconbtn" title="{{ $nextStep->title }}"><i class="fas fa-chevron-right"></i></a>
                    @endif
                </div>
            </div>

            <div class="st-titlerow">
                <span class="st-stepnum">@if($stepIdx){{ $stepIdx }}/{{ $totalSteps ?? '–' }}@else№@endif</span>
                <h1 class="st-title" title="{{ $step->title }}">{{ $step->title }}</h1>
                <span class="st-chip accent"><i class="fas fa-bolt"></i>{{ $step->experience }}</span>
            </div>
        </div>
        <div class="st-thinbar" title="Прогресс курса: {{ $doneCount ?? 0 }}/{{ $totalSteps ?? 0 }} · {{ $progressPercent ?? 0 }}%"><span style="width:{{ $progressPercent ?? 0 }}%"></span></div>
    </section>

    {{-- ================= BODY ================= --}}
    <div class="st-body">
        <main>
            {{-- Табы вместо бесконечной простыни (закреплены + сворачиваются) --}}
            <nav class="st-tabs" aria-label="Разделы шага" x-data="{ navOpen: (localStorage.getItem('st-nav') ?? '1') === '1' }" :class="!navOpen && 'st-tabs--mini'">
                <div class="st-tabs-row" x-show="navOpen" x-collapse>
                @if($hasLecture)
                    <button class="st-tab" :class="activeTab==='theory'?'active':''" @click="activeTab='theory'"><i class="fas fa-book-open"></i>Теория</button>
                @endif
                @if($hasVocab)
                    <button class="st-tab" :class="activeTab==='concepts'?'active':''" @click="activeTab='concepts'"><i class="fas fa-layer-group"></i>Разбор<span class="cnt">{{ $step->vocabularies->count() }}</span></button>
                @endif
                @if($hasTests)
                    <button class="st-tab" :class="activeTab==='practice'?'active':''" @click="activeTab='practice'"><i class="fas fa-tasks"></i>Практика<span class="cnt @if($passedCount===$testsTotal && $testsTotal>0) done @endif">{{ $passedCount }}/{{ $testsTotal }}</span></button>
                @endif
                @if($hasExams)
                    <button class="st-tab" :class="activeTab==='exam'?'active':''" @click="activeTab='exam'"><i class="fas fa-graduation-cap"></i>Экзамен<span class="cnt">{{ $step->exams->count() }}</span></button>
                @endif
                @if($hasSlides)
                    <button class="st-tab" :class="activeTab==='slides'?'active':''" @click="activeTab='slides'"><i class="fas fa-images"></i>Конспект<span class="cnt">{{ $step->slides->count() }}</span></button>
                @endif
                @if($tabsCount === 0)
                    <span class="st-tab active"><i class="fas fa-inbox"></i>Пусто</span>
                @endif
                </div>
                <button type="button" class="st-navfold" @click="navOpen = !navOpen; localStorage.setItem('st-nav', navOpen ? '1' : '0')" :title="navOpen ? 'Свернуть навигацию' : 'Развернуть навигацию'">
                    <i class="fas" :class="navOpen ? 'fa-chevron-up' : 'fa-list'"></i>
                </button>
            </nav>

            {{-- ТЕОРИЯ --}}
            @if($hasLecture)
            <div class="st-panel" x-show="activeTab==='theory'" x-cloak>
                <section class="st-card" x-data="{ open: true }">
                    <button type="button" class="st-sec-head" @click="open = !open" :aria-expanded="open">
                        <i class="fas fa-book-open" style="color:var(--accent)"></i>Теория · ~{{ $readingMinutes ?? 5 }} мин
                        <i class="fas fa-chevron-down st-fold-chev" :class="open && 'rot'"></i>
                    </button>
                    <div x-show="open" x-collapse>
                        <article class="st-article st-article--fold">{!! clean($step->description) !!}</article>
                    </div>
                </section>
                @if($step->links->count() > 0)
                <section class="st-card">
                    <h3 class="st-sec-title"><i class="fas fa-link" style="color:var(--accent);margin-right:6px"></i>Источники для углубления</h3>
                    <div class="st-res-grid">
                        @foreach($step->links as $link)
                            <a href="{{ $link->link }}" target="_blank" rel="noopener" class="st-res-card">
                                <span class="st-res-ic"><i class="fas fa-external-link-alt"></i></span>
                                <span class="st-res-tx"><b>{{ parse_url($link->link, PHP_URL_HOST) }}</b><small>{{ mb_strimwidth($link->link, 0, 60, '…') }}</small></span>
                            </a>
                        @endforeach
                    </div>
                </section>
                @endif
            </div>
            @endif

            {{-- РАЗБОР --}}
            @if($hasVocab)
            <div class="st-panel" x-show="activeTab==='concepts'" x-cloak>
                <section class="st-card">
                    <h3 class="st-sec-title">Ключевые понятия с примерами</h3>
                    <p class="st-sec-sub">Открывайте по порядку — каждый разбор закрепляет кусок теории</p>
                    <div class="st-acc">
                        @foreach($step->vocabularies as $vocab)
                            <details class="st-acc-item" @if($loop->first) open @endif>
                                <summary>
                                    <span class="st-acc-n">{{ $loop->iteration }}</span>
                                    <span class="st-acc-t">{{ $vocab->title }}</span>
                                    <span class="st-xp">{{ $vocab->experience }} XP</span>
                                    <i class="fas fa-chevron-down st-acc-chev"></i>
                                </summary>
                                <div class="st-acc-body st-article">{!! clean($vocab->content) !!}</div>
                                @if($vocab->links->count() > 0)
                                    <div class="st-acc-links">
                                        @foreach($vocab->links as $link)
                                            <a href="{{ $link->link }}" target="_blank" rel="noopener"><i class="fas fa-external-link-alt"></i> {{ parse_url($link->link, PHP_URL_HOST) }}</a>
                                        @endforeach
                                    </div>
                                @endif
                            </details>
                        @endforeach
                    </div>
                </section>
            </div>
            @endif

            {{-- ПРАКТИКА --}}
            @if($hasTests)
            <div class="st-panel" x-show="activeTab==='practice'" x-cloak>
                <section class="st-card">
                    <h3 class="st-sec-title">Практика <span style="color:var(--text-muted);font-weight:600">· {{ $passedCount }}/{{ $testsTotal }} решено</span></h3>
                    <p class="st-sec-sub">Закрепите теорию — за каждое задание начисляется XP</p>
                    <div class="st-pbar"><span style="width:{{ $testsTotal ? round($passedCount / $testsTotal * 100) : 0 }}%"></span></div>
                    <div class="st-listtools">
                        <button type="button" class="st-tool" @click="$dispatch('st-tests', true)">Развернуть все</button>
                        <button type="button" class="st-tool" @click="$dispatch('st-tests', false)">Свернуть все</button>
                    </div>
                    <div class="st-qlist">
                        @foreach($step->tests as $test)
                            @php
                                $passed = in_array($test->id, $testResults ?? []);
                                $lbl = $typeLabels[$test->type_test] ?? [$test->type_test, 'fa-question', ''];
                                $shuffledOpts = $test->type_test === 'matching' ? $test->matchingItems->shuffle() : collect();
                            @endphp
                            <article class="st-q @if($passed) is-passed @endif" id="test-{{ $test->id }}" x-data="{ open: {{ $passed ? 'false' : 'true' }} }" @st-tests.window="open = $event.detail">
                                <button type="button" class="st-q-head" @click="open = !open" :aria-expanded="open">
                                    <span class="st-q-n">Задание {{ $loop->iteration }}</span>
                                    <span class="st-q-type"><i class="fas {{ $lbl[1] }}"></i>{{ $lbl[0] }}</span>
                                    <span class="st-xp">{{ $test->score }} XP</span>
                                    @if($passed)<span class="st-q-ok"><i class="fas fa-check"></i>Решено</span>@endif
                                    <i class="fas fa-chevron-down st-fold-chev" :class="open && 'rot'"></i>
                                </button>
                                <div class="st-q-body" x-show="open" x-collapse>
                                <p class="st-q-text">{{ $test->text }}</p>
                                <p class="st-q-hint">{{ $lbl[2] }}</p>
                                @if(!$passed && $canAct)
                                    <form onsubmit="return stSubmit(event, {{ $test->id }}, '{{ $test->type_test }}')">
                                        @if($test->type_test === 'one_correct')
                                            <div class="st-opts">
                                                @foreach($test->variants as $v)
                                                    <label class="st-opt">
                                                        <input type="radio" name="t{{ $test->id }}" value="{{ e($v->variant) }}" required>
                                                        <span class="st-opt-dot"></span>
                                                        <span>{{ $v->variant }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif($test->type_test === 'list_correct')
                                            <div class="st-opts">
                                                @foreach($test->variants as $v)
                                                    <label class="st-opt">
                                                        <input type="checkbox" name="t{{ $test->id }}[]" value="{{ e($v->variant) }}">
                                                        <span class="st-opt-box"></span>
                                                        <span>{{ $v->variant }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif($test->type_test === 'question_answer')
                                            <input type="text" name="t{{ $test->id }}" required placeholder="Введите ответ…" autocomplete="off" class="st-input">
                                        @elseif($test->type_test === 'true_false')
                                            <div class="st-tf">
                                                <label class="st-tf-card">
                                                    <input type="radio" name="t{{ $test->id }}" value="1" required>
                                                    <i class="fas fa-check"></i><b>Верно</b>
                                                </label>
                                                <label class="st-tf-card st-tf-no">
                                                    <input type="radio" name="t{{ $test->id }}" value="0">
                                                    <i class="fas fa-times"></i><b>Неверно</b>
                                                </label>
                                            </div>
                                        @elseif($test->type_test === 'matching')
                                            <div class="st-match">
                                                @foreach($test->matchingItems as $mi)
                                                    <div class="st-match-row">
                                                        <span class="st-match-l">{{ $mi->list1_item }}</span>
                                                        <i class="fas fa-arrows-alt-h st-match-arr"></i>
                                                        <select name="matching[{{ e($mi->list1_item) }}]" required class="st-select">
                                                            <option value="">Выберите пару…</option>
                                                            @foreach($shuffledOpts as $opt)
                                                                <option value="{{ e($opt->list2_item) }}">{{ $opt->list2_item }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <button type="submit" class="st-btn st-btn-primary st-btn-sm"><i class="fas fa-paper-plane"></i>Проверить ответ</button>
                                    </form>
                                    <div id="r{{ $test->id }}" class="st-fb" hidden></div>
                                @elseif($passed)
                                    <div class="st-fb is-ok"><i class="fas fa-check-circle"></i>Задание выполнено, XP начислен.</div>
                                @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            </div>
            @endif

            {{-- ЭКЗАМЕН --}}
            @if($hasExams)
            <div class="st-panel" x-show="activeTab==='exam'" x-cloak>
                <section class="st-card">
                    <h3 class="st-sec-title">Мини-экзамен</h3>
                    <p class="st-sec-sub">Сначала попробуйте ответить сами, потом сверьтесь с разбором</p>
                    <div class="st-listtools">
                        <button type="button" class="st-tool" @click="$dispatch('st-exams', true)">Развернуть все</button>
                        <button type="button" class="st-tool" @click="$dispatch('st-exams', false)">Свернуть все</button>
                    </div>
                    <div class="st-qlist">
                        @foreach($step->exams as $exam)
                            <article class="st-exam" x-data="{ open: true }" @st-exams.window="open = $event.detail">
                                <button type="button" class="st-q-head" @click="open = !open" :aria-expanded="open">
                                    <span class="st-exam-type">{{ $exam->type }}</span>
                                    <span class="st-xp">{{ $exam->score }} XP</span>
                                    <span class="st-exam-diff">{{ $exam->difficulty }}</span>
                                    <i class="fas fa-chevron-down st-fold-chev" :class="open && 'rot'"></i>
                                </button>
                                <div class="st-q-body" x-show="open" x-collapse>
                                <p class="st-q-text">{{ $exam->question }}</p>
                                @if($exam->options)
                                    <ol class="st-exam-opts">
                                        @foreach($exam->options as $opt)<li>{{ $opt }}</li>@endforeach
                                    </ol>
                                @endif
                                @if($exam->explanation || $exam->correct_answer)
                                    <details class="st-ans">
                                        <summary><i class="fas fa-eye"></i>Показать ответ и разбор</summary>
                                        <div>
                                            <p style="margin:0"><b>Ответ:</b> {{ $exam->correct_answer }}</p>
                                            @if($exam->explanation)<p class="st-ans-exp">{{ $exam->explanation }}</p>@endif
                                        </div>
                                    </details>
                                @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            </div>
            @endif

            {{-- КОНСПЕКТ --}}
            @if($hasSlides)
            <div class="st-panel" x-show="activeTab==='slides'" x-cloak>
                <section class="st-card">
                    <h3 class="st-sec-title">Конспект для повторения</h3>
                    <p class="st-sec-sub">Коротко главное из шага</p>
                    <div class="st-slides">
                        @foreach($step->slides->sortBy('sort_order') as $slide)
                            <article class="st-slide">
                                <header><span>{{ $loop->iteration }}</span><h3>{{ $slide->title }}</h3></header>
                                <div class="st-article">{!! clean($slide->content) !!}</div>
                            </article>
                        @endforeach
                    </div>
                </section>
            </div>
            @endif

            {{-- Пустое состояние: ничего не сгенерировано --}}
            @if($tabsCount === 0)
            <div class="st-panel">
                <div class="st-card st-empty">
                    <i class="fas fa-wand-magic-sparkles"></i>
                    <b>Контент шага ещё не готов</b>
                    <p>AI-контент по этой теме пока не сгенерирован.</p>
                    @if($isOwner)
                        <button onclick="stGen('all', this)" class="st-btn st-btn-primary st-btn-sm"><i class="fas fa-magic"></i>Сгенерировать всё</button>
                    @else
                        <p>Загляните чуть позже — материал уже готовится.</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Пустые подразделы с точечной генерацией для владельца --}}
            @if($isOwner)
                @if(!$hasLecture)
                <div class="st-card st-empty" x-show="activeTab==='theory'" x-cloak>
                    <i class="fas fa-book-open"></i><b>Лекция ещё не готова</b><p>Сгенерируйте теорию отдельно.</p>
                    <button onclick="stGen('description', this)" class="st-btn st-btn-primary st-btn-sm"><i class="fas fa-magic"></i>Сгенерировать лекцию</button>
                </div>
                @endif
            @endif

            {{-- Нижняя навигация --}}
            <div style="margin-top:18px;display:flex;flex-direction:column;gap:12px">
                @if($step->children->count() > 0)
                    <div class="st-next-list">
                        @foreach($step->children as $child)
                            <a href="{{ route('courses.step', [$course->id, $child->id]) }}" class="st-next-card">
                                <span class="st-next-ic"><i class="fas fa-arrow-right"></i></span>
                                <span><b>{{ $child->title }}</b><br><small>{{ $child->experience }} XP · следующий подшаг</small></span>
                            </a>
                        @endforeach
                    </div>
                @endif
                <div class="st-bottom-nav">
                    @if(!empty($prevStep))
                        <a href="{{ route('courses.step', [$course->id, $prevStep->id]) }}" class="st-btn st-btn-ghost"><i class="fas fa-arrow-left"></i>{{ mb_strimwidth($prevStep->title, 0, 26, '…') }}</a>
                    @else
                        <a href="{{ route('courses.show', $course->id) }}" class="st-btn st-btn-ghost"><i class="fas fa-list"></i>К содержанию</a>
                    @endif
                    @if($isDone)
                        @if(!empty($nextStep))
                            <a href="{{ route('courses.step', [$course->id, $nextStep->id]) }}" class="st-btn st-btn-primary">Далее: {{ mb_strimwidth($nextStep->title, 0, 22, '…') }}<i class="fas fa-arrow-right"></i></a>
                        @else
                            <a href="{{ route('courses.show', $course->id) }}" class="st-btn st-btn-primary"><i class="fas fa-list"></i>К содержанию курса</a>
                        @endif
                    @elseif($canAct)
                        <button onclick="stComplete()" class="st-btn st-btn-primary"><i class="fas fa-flag-checkered"></i>Я изучил — завершить шаг</button>
                    @endif
                </div>
            </div>
        </main>

        {{-- ================= SIDEBAR ================= --}}
        <aside class="st-side">
            <div class="st-card">
                <h3>Прогресс курса</h3>
                <div class="st-progress-top"><span style="color:var(--text-muted)">{{ $doneCount ?? 0 }}/{{ $totalSteps ?? 0 }} шагов</span><b>{{ $progressPercent ?? 0 }}%</b></div>
                <div class="st-progress"><span style="width:{{ $progressPercent ?? 0 }}%"></span></div>
                <div style="margin-top:14px">
                    @if($isDone)
                        <span class="st-btn st-btn-done st-btn-sm" style="width:100%"><i class="fas fa-check"></i>Шаг пройден</span>
                    @elseif($canAct)
                        <button onclick="stComplete()" class="st-btn st-btn-primary st-btn-sm" style="width:100%"><i class="fas fa-flag-checkered"></i>Завершить шаг</button>
                    @endif
                </div>
            </div>

            @if(($courseSteps ?? collect())->count())
                <div class="st-card">
                    <h3>Шаги курса <small>{{ $doneCount ?? 0 }}/{{ $totalSteps ?? 0 }}</small></h3>
                    <div class="st-outline">
                        @foreach(($courseSteps ?? collect()) as $s)
                            @php $d = in_array($s->id, $completedStepIds ?? []); $cur = (int) $s->id === (int) $step->id; @endphp
                            <a href="{{ route('courses.step', [$course->id, $s->id]) }}" class="@if($cur) cur @endif @if($d) done @endif" @if($cur) aria-current="page" @endif>
                                <span class="st-o-check">@if($d)<i class="fas fa-check"></i>@else{{ $loop->iteration }}@endif</span>
                                <span class="st-o-t">{{ $s->title }}</span>
                                <span class="st-o-xp">{{ $s->experience }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="st-card st-tip">
                <h3 style="color:var(--accent)"><i class="fas fa-lightbulb"></i> Как учиться эффективно</h3>
                <ul>
                    <li>Сначала теория — не прыгайте сразу к практике</li>
                    <li>Разбирайте понятия по порядку</li>
                    <li>Ошиблись в тесте — вернитесь к теории выше</li>
                </ul>
            </div>
        </aside>
    </div>

    {{-- Мобильная панель --}}
    <div class="st-mobilebar">
        @if($isDone)
            <span class="st-btn st-btn-done st-btn-sm"><i class="fas fa-check"></i>Пройден</span>
        @elseif($canAct)
            <button onclick="stComplete()" class="st-btn st-btn-primary st-btn-sm"><i class="fas fa-flag-checkered"></i>Завершить</button>
        @endif
        @if(!empty($nextStep))
            <a href="{{ route('courses.step', [$course->id, $nextStep->id]) }}" class="st-btn st-btn-ghost st-btn-sm">Далее<i class="fas fa-arrow-right"></i></a>
        @else
            <a href="{{ route('courses.show', $course->id) }}" class="st-btn st-btn-ghost st-btn-sm"><i class="fas fa-list"></i>Курс</a>
        @endif
    </div>
</div>

<script>
(function(){
    // Кнопка «скопировать» для всех pre>code
    document.querySelectorAll('.st-article pre').forEach(function(pre){
        var code = pre.querySelector('code');
        if (!code) return;
        pre.parentElement.classList.add('st-codewrap');
        var btn = document.createElement('button');
        btn.type = 'button'; btn.className = 'st-copy'; btn.textContent = 'Копировать';
        btn.onclick = function(){
            navigator.clipboard.writeText(code.innerText).then(function(){ btn.textContent = 'Скопировано!'; setTimeout(function(){ btn.textContent = 'Копировать'; }, 1500); });
        };
        pre.appendChild(btn);
    });

    // Подсветка выбранных вариантов
    document.querySelectorAll('.st-opt input, .st-tf-card input').forEach(function(inp){
        inp.addEventListener('change', function(){
            var name = inp.getAttribute('name');
            document.querySelectorAll('input[name="' + name + '"]').forEach(function(o){
                var card = o.closest('.st-opt, .st-tf-card');
                if (card) card.classList.toggle('picked', o.checked);
            });
        });
    });
})();

function stCsrf(){ var m = document.querySelector('meta[name="csrf-token"]'); return m ? m.content : ''; }

function stComplete(){
    var btn = document.getElementById('stCompleteBtn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Сохраняем…'; }
    fetch('{{ route("courses.step.complete", [$course->id, $step->id]) }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':stCsrf(),'Accept':'application/json'}
    }).then(function(r){ return r.json(); }).then(function(data){
        if (data.success) location.reload();
        else if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-flag-checkered"></i>Завершить шаг'; }
    }).catch(function(){ if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-flag-checkered"></i>Завершить шаг'; } });
}

function stSubmit(e, testId, type){
    e.preventDefault();
    var form = e.target, answer;
    if (type === 'one_correct') { var c = form.querySelector('input[type="radio"]:checked'); if(!c) return false; answer = c.value; }
    else if (type === 'list_correct') { answer = Array.from(form.querySelectorAll('input[type="checkbox"]:checked')).map(function(x){ return x.value; }); if(!answer.length) return false; }
    else if (type === 'question_answer') { answer = form.querySelector('input[type="text"]').value; if(!answer.trim()) return false; }
    else if (type === 'true_false') { var t = form.querySelector('input[type="radio"]:checked'); if(!t) return false; answer = t.value; }
    else if (type === 'matching') {
        answer = [];
        var ok = true;
        form.querySelectorAll('select').forEach(function(s){ if(!s.value) ok = false; else answer.push({list1: s.name.replace('matching[','').replace(/\]$/,''), list2: s.value}); });
        if(!ok) return false;
    }
    var btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Проверяем…'; }
    fetch('{{ route("courses.step.test", [$course->id, $step->id, "__ID__"]) }}'.replace('__ID__', testId), {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':stCsrf(),'Accept':'application/json'},
        body: JSON.stringify({answer: answer})
    }).then(function(r){ return r.json(); }).then(function(data){
        var el = document.getElementById('r' + testId);
        el.hidden = false;
        if (data.is_correct) {
            el.className = 'st-fb is-ok';
            el.innerHTML = '<i class="fas fa-check-circle"></i>Правильно! +' + data.score + ' XP';
            setTimeout(function(){ location.reload(); }, 1200);
        } else {
            el.className = 'st-fb is-err';
            el.innerHTML = '<i class="fas fa-times-circle"></i>Не совсем — попробуйте ещё раз.'
                + ((data.correct_answer && data.correct_answer.length) ? '<small>Подсказка — правильный ответ: ' + data.correct_answer.join(', ') + '</small>' : '');
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane"></i>Проверить ответ'; }
        }
    }).catch(function(){ if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane"></i>Проверить ответ'; } });
    return false;
}

function stGen(type, btn){
    var o = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Запускаем AI…'; }
    fetch('{{ route("courses.step.generate", [$course->id, $step->id]) }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':stCsrf(),'Accept':'application/json'},
        body: JSON.stringify({type: type})
    }).then(function(r){ return r.json(); }).then(function(d){
        alert(d.message || 'Генерация запущена. Обновите страницу через минуту.');
        location.reload();
    }).catch(function(){ alert('Не удалось запустить генерацию.'); if (btn) { btn.disabled = false; btn.innerHTML = o; } });
}
</script>
@endsection
