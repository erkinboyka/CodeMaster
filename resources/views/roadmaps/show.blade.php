@extends('layouts.app')

@section('title', ($roadmap->title ?? 'Roadmap') . ' - CodeMaster')

@section('content')
<div id="app" class="rm-app">
    <header class="rm-header">
        <nav class="rm-breadcrumb">
            <a href="{{ route('roadmaps.index') }}" class="rm-bc-link"></i> {{ __('Roadmaps') }}</a>
            
            <span class="rm-bc-current">{{ $roadmap->title }}</span>
        </nav>

        @if($prevRoadmap || $nextRoadmap)
        <div class="rm-nav-arrows">
            @if($prevRoadmap)
            <a href="{{ route('roadmap.show', $prevRoadmap) }}" class="rm-nav-btn" title="{{ $prevRoadmap }}">
                <i class="fas fa-chevron-left"></i>
                <span>{{ $prevRoadmap }}</span>
            </a>
            @else
            <div></div>
            @endif
            @if($nextRoadmap)
            <a href="{{ route('roadmap.show', $nextRoadmap) }}" class="rm-nav-btn rm-nav-btn--right" title="{{ $nextRoadmap }}">
                <span>{{ $nextRoadmap }}</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            @endif
        </div>
        @endif

        <div class="rm-badge reveal-up" data-delay="0">ROADMAP STUDIO</div>
        <h1 class="rm-title reveal-up" data-delay="0.1">{{ $roadmap->title }}</h1>
        <div class="rm-stats reveal-up" data-delay="0.2">
            <span>{{ $totalNodes }} {{ __('topics') }}</span>
            <span>&middot;</span>
            <span>{{ count($completedNodeIds) }}/{{ $totalNodes }} {{ __('done') }}</span>
            <span>&middot;</span>
            <span>{{ $percent }}%</span>
        </div>
    </header>

    <main class="rm-main reveal-up" data-delay="0.3">
        <div class="rm-canvas" id="rmCanvas">
            <svg id="rmSvg" class="rm-svg"></svg>
            <div id="rmNodes" class="rm-nodes">
                @foreach($roadmap->nodes as $node)
                    @php
                        $deps = $node->deps;
                        if (is_string($deps)) $deps = json_decode($deps, true);
                        if (!is_array($deps)) $deps = [];
                        $deps = array_values(array_filter($deps));
                        $isDone = in_array($node->id, $completedNodeIds);
                        $depsMet = count($deps) === 0 || collect($deps)->every(fn($d) => in_array($d, $completedNodeIds));
                        $status = $isDone ? 'completed' : ($depsMet ? 'available' : 'locked');
                    @endphp
                    <div class="rm-node rm-node--{{ $status }}"
                         data-id="{{ $node->id }}"
                         data-status="{{ $status }}"
                         data-deps="{{ json_encode($deps) }}"
                         data-title="{{ addslashes($node->title) }}"
                         data-topic="{{ addslashes($node->topic ?? '') }}"
                         data-course="{{ $node->course_id ?? '' }}"
                         data-exam="{{ $node->is_exam ? '1' : '0' }}"
                         data-materials="{{ addslashes(json_encode($node->materials ?? [])) }}"
                         style="left:{{ $node->x }}px;top:{{ $node->y }}px;">
                        <div class="rm-node-topic">{{ $node->topic ?? 'Topic' }}</div>
                        <div class="rm-node-name">{{ $node->title }}</div>
                        <div class="rm-node-tags">
                            @if($node->is_exam)
                            <span class="rm-tag rm-tag--exam">EXAM</span>
                            @elseif($node->course_id)
                            <span class="rm-tag rm-tag--course">COURSE</span>
                            @endif
                            @if($isDone)
                            <span class="rm-tag rm-tag--done">DONE</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
</div>

<div id="rmModal" class="rm-modal">
    <div class="rm-modal-dialog">
        <header class="rm-modal-head">
            <div>
                <div id="rmMTopic" class="rm-m-topic"></div>
                <h2 id="rmMTitle" class="rm-m-title"></h2>
            </div>
            <button class="rm-m-close" onclick="rmClose()"><i class="fas fa-times"></i></button>
        </header>
        <div class="rm-modal-content">
            <div id="rmTheorySection">
                <h3 class="rm-section-title">{{ __('Lessons & Materials') }}</h3>
                <ul id="rmMaterials" class="rm-materials"></ul>
            </div>
            <div id="rmQuizSection" class="rm-hidden">
                <div class="rm-sep"></div>
                <h3 class="rm-section-title" id="rmQuizTitle">{{ __('Mini Test') }}</h3>
                <div id="rmMiniTest" class="rm-quiz"></div>
                <p id="rmMiniResult" class="rm-quiz-result"></p>
            </div>
        </div>
        <footer class="rm-modal-foot">
            <button id="rmReadBtn" onclick="rmMarkRead()" class="rm-btn rm-btn--sky">{{ __('I have read everything') }}</button>
            <button id="rmCheckBtn" onclick="rmCheckQuiz()" class="rm-btn rm-btn--green rm-hidden">{{ __('Check') }}</button>
        </footer>
    </div>
</div>

<style>
.rm-app{font-family:'Space Grotesk',system-ui,sans-serif;background:var(--rm-bg);min-height:100vh;padding:1.5rem 1rem}
.rm-header{text-align:center;margin-bottom:1.5rem;position:relative}
.rm-breadcrumb{display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:12px;font-size:13px}
.rm-bc-link{color:var(--rm-text-dim);text-decoration:none;transition:.2s;display:inline-flex;align-items:center;gap:6px}
.rm-bc-link:hover{color:var(--rm-accent)}
.rm-bc-sep{font-size:10px;color:var(--rm-text-dim)}
.rm-bc-current{color:var(--rm-text);font-weight:600}
.rm-nav-arrows{display:flex;justify-content:space-between;gap:12px;margin:12px 0}
.rm-nav-btn{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:10px;font-size:13px;font-weight:600;color:var(--rm-text-dim);text-decoration:none;border:1px solid var(--rm-border);transition:.2s;max-width:45%;overflow:hidden}
.rm-nav-btn span{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.rm-nav-btn:hover{color:var(--rm-accent);border-color:var(--rm-accent);background:rgba(56,189,248,.06)}
.rm-nav-btn--right{margin-left:auto;text-align:right}
.rm-badge{display:inline-block;padding:6px 16px;border-radius:999px;background:var(--rm-card);color:var(--rm-text);font-size:11px;letter-spacing:.08em;text-transform:uppercase;font-weight:600;border:1px solid var(--rm-border)}
.rm-title{font-size:clamp(1.8rem,4vw,3rem);font-weight:700;color:var(--rm-text);margin:.75rem 0 0}
.rm-stats{margin-top:.5rem;color:var(--rm-text-dim);font-size:.875rem;display:flex;gap:8px;justify-content:center}
.rm-main{background:var(--rm-card);border:1px solid var(--rm-border);border-radius:1rem;box-shadow:0 25px 50px rgba(0,0,0,.25);padding:2rem;min-height:70vh;width:100%;max-width:80rem;margin:0 auto}
.rm-canvas{position:relative;width:100%;height:780px;overflow:auto;border-radius:.75rem;cursor:grab;user-select:none}
.rm-canvas:active{cursor:grabbing}
.rm-svg{position:absolute;top:0;left:0;pointer-events:none}
.rm-nodes{position:absolute;top:0;left:0}
.rm-node{
    position:absolute;width:210px;min-height:80px;padding:12px 16px;border-radius:12px;
    display:flex;flex-direction:column;align-items:flex-start;justify-content:center;
    font-weight:600;text-align:left;border:2px solid var(--rm-border);
    transition:all .3s ease;cursor:pointer;box-shadow:0 4px 12px rgba(0,0,0,.15);
    background:var(--rm-card);z-index:2;
}
.rm-node-topic{font-size:10px;text-transform:uppercase;letter-spacing:.06em;color:var(--rm-text-dim);margin-bottom:2px}
.rm-node-name{font-size:14px;color:var(--rm-text)}
.rm-node-tags{display:flex;gap:6px;margin-top:8px;flex-wrap:wrap}
.rm-tag{padding:2px 8px;border-radius:999px;font-size:9px;text-transform:uppercase;font-weight:700;letter-spacing:.04em}
.rm-tag--exam{background:rgba(168,85,247,.15);color:#a855f7}
.rm-tag--course{background:rgba(56,189,248,.12);color:var(--rm-accent)}
.rm-tag--done{background:rgba(34,197,94,.15);color:#22c55e}
.rm-node--completed{background:rgba(34,197,94,.12)!important;border-color:#22c55e!important}
.rm-node--completed:hover{transform:scale(1.03)}
.rm-node--available{background:var(--rm-card);border-color:var(--rm-border);color:var(--rm-text)}
.rm-node--available:hover{border-color:var(--rm-accent);transform:scale(1.03);box-shadow:0 8px 24px rgba(0,0,0,.2)}
.rm-node--locked{background:var(--rm-surface);color:var(--rm-text-dim);cursor:not-allowed;opacity:.55}
/* Modal */
.rm-modal{position:fixed;inset:0;background:rgba(0,0,0,.7);display:none;align-items:center;justify-content:center;padding:1rem;z-index:200}
.rm-modal.open{display:flex}
.rm-modal-dialog{background:var(--rm-surface);border:1px solid var(--rm-border);border-radius:1rem;box-shadow:0 25px 60px rgba(0,0,0,.4);width:100%;max-width:48rem;max-height:90vh;display:flex;flex-direction:column;animation:rmIn .2s ease-out}
@@keyframes rmIn{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
.rm-modal-head{display:flex;align-items:center;justify-content:space-between;padding:1.5rem;border-bottom:1px solid var(--rm-border)}
.rm-m-topic{font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--rm-text-dim)}
.rm-m-title{font-size:1.5rem;font-weight:700;color:var(--rm-text)}
.rm-m-close{color:var(--rm-text-dim);background:none;border:none;cursor:pointer;font-size:1.5rem;padding:8px;border-radius:8px;transition:.2s}
.rm-m-close:hover{background:rgba(56,189,248,.1);color:var(--rm-accent)}
.rm-modal-content{padding:1.5rem;overflow-y:auto;flex:1;color:var(--rm-text)}
.rm-sep{border-top:1px solid var(--rm-border);margin:1rem 0}
.rm-section-title{font-size:1rem;font-weight:600;color:var(--rm-text);margin-bottom:.75rem}
.rm-materials{list-style:none;padding:0;margin:0}
.rm-materials li{margin-bottom:8px}
.rm-materials a{color:var(--rm-accent);text-decoration:underline;text-underline-offset:2px;transition:.2s}
.rm-materials a:hover{opacity:.8}
.rm-quiz{display:flex;flex-direction:column;gap:8px}
.rm-quiz-result{margin-top:8px;font-size:13px}
.rm-quiz-opt{display:block;border:1px solid var(--rm-border);border-radius:8px;padding:10px 14px;cursor:pointer;transition:.2s;font-size:14px;color:var(--rm-text);background:var(--rm-card)}
.rm-quiz-opt:hover{border-color:var(--rm-accent)}
.rm-quiz-opt.rm-sel{border-color:var(--rm-accent);background:rgba(56,189,248,.08)}
.rm-modal-foot{padding:1rem 1.5rem;border-top:1px solid var(--rm-border);display:flex;gap:12px}
.rm-btn{flex:1;padding:12px;border-radius:10px;font-size:14px;font-weight:700;border:none;cursor:pointer;transition:.2s;font-family:inherit}
.rm-btn--sky{background:#38bdf8;color:#0f172a}
.rm-btn--sky:hover{background:#7dd3fc}
.rm-btn--green{background:#22c55e;color:#fff}
.rm-btn--green:hover{background:#4ade80}
.rm-hidden{display:none!important}
.rm-read-done{background:#22c55e!important;color:#fff!important}
@@media(max-width:640px){.rm-main{padding:1rem}.rm-canvas{height:500px}}
</style>

<script>
(function(){
    var RM={bg:'#0f172a',card:'#1e293b',surface:'#1e293b',border:'#334155',text:'#e2e8f0',textDim:'#94a3b8',accent:'#38bdf8'};
    function setVars(){var r=document.documentElement;for(var k in RM)r.style.setProperty('--rm-'+k,RM[k])}
    function isLight(){return(document.documentElement.getAttribute('data-theme')||'').indexOf('light')!==-1}
    function sync(){if(isLight()){RM={bg:'#f8fafc',card:'#fff',surface:'#fff',border:'#e2e8f0',text:'#1e293b',textDim:'#64748b',accent:'#0284c7'}}else{RM={bg:'#0f172a',card:'#1e293b',surface:'#1e293b',border:'#334155',text:'#e2e8f0',textDim:'#94a3b8',accent:'#38bdf8'}}setVars()}
    sync();
    new MutationObserver(sync).observe(document.documentElement,{attributes:true,attributeFilter:['data-theme']});

    var nodes=document.querySelectorAll('.rm-node');
    var svg=document.getElementById('rmSvg');
    var canvas=document.getElementById('rmCanvas');
    var modal=document.getElementById('rmModal');
    var curId=null;

    function gDeps(el){try{return JSON.parse(el.dataset.deps||'[]')}catch(e){return[]}}
    function isDone(el){return el.dataset.status==='completed'}

    function drawLines(){
        svg.innerHTML='';
        var ns='http://www.w3.org/2000/svg';
        var defs=document.createElementNS(ns,'defs');
        [[RM.accent,'arrA'],[RM.textDim,'arrL'],['#22c55e','arrD']].forEach(function(m){
            var mk=document.createElementNS(ns,'marker');
            mk.setAttribute('id',m[1]);mk.setAttribute('markerWidth','10');mk.setAttribute('markerHeight','8');
            mk.setAttribute('refX','10');mk.setAttribute('refY','4');mk.setAttribute('orient','auto');
            var p=document.createElementNS(ns,'path');p.setAttribute('d','M0,0 L10,4 L0,8');p.setAttribute('fill',m[0]);
            mk.appendChild(p);defs.appendChild(mk);
        });
        svg.appendChild(defs);

        var svgW=0,svgH=0;
        nodes.forEach(function(c){
            var deps=gDeps(c);
            deps.forEach(function(did){
                var par=document.querySelector('.rm-node[data-id="'+did+'"]');
                if(!par)return;
                var x1=par.offsetLeft+par.offsetWidth;
                var y1=par.offsetTop+par.offsetHeight/2;
                var x2=c.offsetLeft;
                var y2=c.offsetTop+c.offsetHeight/2;
                if(x2>x1){
                    var cpx1=x1+(x2-x1)*0.5;
                    var cpx2=x2-(x2-x1)*0.5;
                    var d='M'+x1+','+y1+' C'+cpx1+','+y1+' '+cpx2+','+y2+' '+x2+','+y2;
                }else{
                    var midX=(x1+x2)/2;
                    var midY=(y1+y2)/2;
                    var d='M'+x1+','+y1+' Q'+(x1+60)+','+y1+' '+(x1+60)+','+midY+' T'+x2+','+y2;
                }
                var path=document.createElementNS(ns,'path');
                path.setAttribute('d',d);path.setAttribute('fill','none');
                path.setAttribute('stroke-width','2.5');path.setAttribute('stroke-linecap','round');
                path.style.transition='all .4s';
                var pd=par.dataset.status==='completed';
                var cd=c.dataset.status==='completed';
                if(pd&&cd){path.setAttribute('stroke','#22c55e');path.setAttribute('marker-end','url(#arrD)')}
                else if(pd){path.setAttribute('stroke',RM.accent);path.setAttribute('marker-end','url(#arrA)');path.setAttribute('stroke-dasharray','0')}
                else{path.setAttribute('stroke','rgba(148,163,184,0.45)');path.setAttribute('marker-end','url(#arrL)');path.setAttribute('stroke-dasharray','6 6')}
                path.style.pointerEvents='none';
                svg.appendChild(path);
                svgW=Math.max(svgW,x2+220);svgH=Math.max(svgH,y2+120);
            });
        });
        svg.setAttribute('width',Math.max(svgW,1400));
        svg.setAttribute('height',Math.max(svgH,780));
    }

    function updateAll(){
        nodes.forEach(function(el){
            var deps=gDeps(el);
            var met=deps.length===0||deps.every(function(d){var p=document.querySelector('.rm-node[data-id="'+d+'"]');return p&&isDone(p)});
            el.dataset.status=isDone(el)?'completed':(met?'available':'locked');
            el.className='rm-node rm-node--'+el.dataset.status;
        });
        drawLines();
    }
    updateAll();

    nodes.forEach(function(el){
        el.addEventListener('click',function(){if(el.dataset.status!=='locked')openModal(el)});
    });

    var quizData={!! json_encode($quizData) !!};
    var lessonsData={!! json_encode($lessonsData) !!};

    function openModal(el){
        curId=el.dataset.id;
        document.getElementById('rmMTopic').textContent=el.dataset.topic||'Topic';
        document.getElementById('rmMTitle').textContent=el.dataset.title||'Node';

        var matList=document.getElementById('rmMaterials');
        matList.innerHTML='';
        var hasAny=false;

        var nodeMats=[];
        try{nodeMats=JSON.parse(el.dataset.materials||'[]')}catch(e){}
        nodeMats.forEach(function(m){
            hasAny=true;
            var li=document.createElement('li');
            var a=document.createElement('a');a.href=m.url||'#';a.textContent=m.label||'Link';a.target='_blank';a.rel='noopener';
            li.appendChild(a);matList.appendChild(li);
        });

        var nodeLes=lessonsData.filter(function(l){return l.node_id==curId});
        nodeLes.forEach(function(l){
            hasAny=true;
            var li=document.createElement('li');
            li.style.cssText='padding:12px 0;border-bottom:1px solid var(--rm-border)';
            var titleHtml='<div style="font-weight:600;color:var(--rm-text);margin-bottom:6px;font-size:15px">'+(l.title||'Lesson')+'</div>';
            var descHtml='<div style="font-size:14px;color:var(--rm-text-dim);line-height:1.7">'+(l.description||'')+'</div>';
            li.innerHTML=titleHtml+descHtml;
            if(l.materials){
                var matsText='';
                if(typeof l.materials==='string' && l.materials.trim()){
                    matsText=l.materials;
                }else if(Array.isArray(l.materials)){
                    matsText=l.materials.map(function(m){return m.label||m.title||m}).join(', ');
                }
                if(matsText){
                    li.innerHTML+='<div style="font-size:12px;color:var(--rm-accent);margin-top:6px">'+matsText+'</div>';
                }
            }
            matList.appendChild(li);
        });

        if(!hasAny)matList.innerHTML='<li style="color:var(--rm-text-dim);font-style:italic">{{ __("Materials are being added.") }}</li>';

        // Hide quiz section, show read button
        var quizSection=document.getElementById('rmQuizSection');
        var readBtn=document.getElementById('rmReadBtn');
        var checkBtn=document.getElementById('rmCheckBtn');
        var miniRes=document.getElementById('rmMiniResult');
        var miniEl=document.getElementById('rmMiniTest');
        quizSection.classList.add('rm-hidden');
        readBtn.classList.remove('rm-hidden');
        checkBtn.classList.add('rm-hidden');
        readBtn.classList.remove('rm-read-done');
        readBtn.textContent='{{ __("I have read everything") }}';
        miniRes.textContent='';miniEl.innerHTML='';

        var isExam=el.dataset.exam==='1';
        var nodeQs=quizData.filter(function(q){return q.node_id==curId});

        // Store quiz data for this node
        window._rmNodeQs=nodeQs;
        window._rmIsExam=isExam;

        if(isExam){
            document.getElementById('rmQuizTitle').textContent='{{ __("Exam") }}';
        }else{
            document.getElementById('rmQuizTitle').textContent='{{ __("Mini Test") }}';
        }

        modal.classList.add('open');document.body.style.overflow='hidden';
    }

    window.rmMarkRead=function(){
        var quizSection=document.getElementById('rmQuizSection');
        var readBtn=document.getElementById('rmReadBtn');
        var checkBtn=document.getElementById('rmCheckBtn');
        var miniEl=document.getElementById('rmMiniTest');
        var nodeQs=window._rmNodeQs||[];
        var isExam=window._rmIsExam;

        readBtn.classList.add('rm-read-done');
        readBtn.textContent='{{ __("Theory completed!") }}';
        readBtn.classList.add('rm-hidden');
        quizSection.classList.remove('rm-hidden');
        checkBtn.classList.remove('rm-hidden');

        if(isExam){
            // Show all exam questions
            if(nodeQs.length>0){
                miniEl.innerHTML=nodeQs.map(function(q,i){
                    var opts=typeof q.options==='string'?JSON.parse(q.options):(q.options||[]);
                    return '<div style="margin-bottom:14px"><p style="font-weight:600;margin-bottom:8px">'+(i+1)+'. '+q.question+'</p>'+
                        opts.map(function(o,j){var u='ex'+curId+'-'+i+'-'+j;return '<div class="rm-quiz-opt" onclick="rmSel(this,\''+u+'\')"><input type="radio" name="rmE'+i+'" value="'+o+'" id="'+u+'" style="display:none"><label for="'+u+'">'+o+'</label></div>'}).join('')+'</div>';
                }).join('');
            }else miniEl.innerHTML='<p style="color:var(--rm-text-dim);font-style:italic">{{ __("Exam is being prepared.") }}</p>';
        }else{
            // Show first quiz question as mini test
            if(nodeQs.length>0){
                var q=nodeQs[0];var opts=typeof q.options==='string'?JSON.parse(q.options):(q.options||[]);
                miniEl.innerHTML='<p style="margin-bottom:10px;font-weight:600;font-size:15px">'+q.question+'</p>'+
                    opts.map(function(o,i){var u='mq'+curId+'-'+i;return '<div class="rm-quiz-opt" onclick="rmSel(this,\''+u+'\')"><input type="radio" name="rmMini" value="'+o+'" id="'+u+'" style="display:none"><label for="'+u+'">'+o+'</label></div>'}).join('');
            }else miniEl.innerHTML='<p style="color:var(--rm-text-dim);font-style:italic">{{ __("Quiz is being prepared.") }}</p>';
        }
    };

    window.rmSel=function(div,uid){var r=document.getElementById(uid);if(r)r.checked=true;div.parentElement.querySelectorAll('.rm-quiz-opt').forEach(function(s){s.classList.remove('rm-sel')});div.classList.add('rm-sel')};
    window.rmClose=function(){modal.classList.remove('open');document.body.style.overflow='';curId=null};
    modal.addEventListener('click',function(e){if(e.target===modal)rmClose()});
    document.addEventListener('keydown',function(e){if(e.key==='Escape')rmClose()});

    var cv=document.getElementById('rmCanvas');
    var dragIs=false,dragX,dragY,dragSL,dragST,dragVelX=0,dragVelY=0,dragRaf=null;
    cv.addEventListener('mousedown',function(e){
        if(e.target.closest('.rm-node'))return;
        dragIs=true;dragX=e.pageX;dragY=e.pageY;dragSL=cv.scrollLeft;dragST=cv.scrollTop;dragVelX=0;dragVelY=0;
        if(dragRaf)cancelAnimationFrame(dragRaf);
    });
    cv.addEventListener('mouseleave',function(){dragIs=false});
    cv.addEventListener('mouseup',function(){
        dragIs=false;
        function mom(){
            if(Math.abs(dragVelX)<0.5&&Math.abs(dragVelY)<0.5)return;
            cv.scrollLeft-=dragVelX;cv.scrollTop-=dragVelY;
            dragVelX*=0.92;dragVelY*=0.92;
            dragRaf=requestAnimationFrame(mom);
        }
        mom();
    });
    cv.addEventListener('mousemove',function(e){
        if(!dragIs)return;
        var dx=e.pageX-dragX,dy=e.pageY-dragY;
        dragVelX=dx*0.3;dragVelY=dy*0.3;
        cv.scrollLeft=dragSL-dx;cv.scrollTop=dragST-dy;
    });

    var tSX,tSY,tSL,tST,tVX=0,tVY=0,tRaf=null;
    cv.addEventListener('touchstart',function(e){
        if(e.target.closest('.rm-node'))return;
        tSX=e.touches[0].pageX;tSY=e.touches[0].pageY;tSL=cv.scrollLeft;tST=cv.scrollTop;tVX=0;tVY=0;
        if(tRaf)cancelAnimationFrame(tRaf);
    },{passive:true});
    cv.addEventListener('touchend',function(){
        function mom(){
            if(Math.abs(tVX)<0.5&&Math.abs(tVY)<0.5)return;
            cv.scrollLeft-=tVX;cv.scrollTop-=tVY;
            tVX*=0.92;tVY*=0.92;
            tRaf=requestAnimationFrame(mom);
        }
        mom();
    });
    cv.addEventListener('touchmove',function(e){
        var dx=e.touches[0].pageX-tSX,dy=e.touches[0].pageY-tSY;
        tVX=dx*0.3;tVY=dy*0.3;
        cv.scrollLeft=tSL-dx;cv.scrollTop=tST-dy;
    },{passive:true});

    window.rmCheckQuiz=function(){
        var res=document.getElementById('rmMiniResult');
        var nodeQs=window._rmNodeQs||[];
        var isExam=window._rmIsExam;

        if(isExam){
            // Check all exam questions
            if(!nodeQs.length){res.textContent='{{ __("No exam available.") }}';res.style.color=RM.textDim;return}
            var ok=0;nodeQs.forEach(function(q,i){var ch=document.querySelector('input[name="rmE'+i+'"]:checked');if(ch&&ch.value===q.correct_answer)ok++});
            var pct=Math.round((ok/nodeQs.length)*100);
            if(pct>=70){res.textContent='{{ __("Exam passed:") }} '+pct+'% ('+ok+'/'+nodeQs.length+')';res.style.color='#22c55e';
                fetch('/roadmap/complete-node',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},body:JSON.stringify({node_id:curId})}).then(function(r){return r.json()}).then(function(d){if(d.percent!==undefined)location.reload()});
            }else{res.textContent='{{ __("Exam failed:") }} '+pct+'% ('+ok+'/'+nodeQs.length+')';res.style.color='#f43f5e'}
        }else{
            // Check mini test (first question only)
            var ch=document.querySelector('input[name="rmMini"]:checked');
            if(!ch){res.textContent='{{ __("Select an answer.") }}';res.style.color=RM.accent;return}
            if(!nodeQs.length)return;
            if(ch.value===nodeQs[0].correct_answer){
                res.textContent='{{ __("Correct! Node completed.") }}';res.style.color='#22c55e';
                fetch('/roadmap/complete-node',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},body:JSON.stringify({node_id:curId})}).then(function(r){return r.json()}).then(function(d){if(d.percent!==undefined)location.reload()});
            }else{res.textContent='{{ __("Wrong answer. Try again.") }}';res.style.color='#f43f5e'}
        }
    };
})();
</script>
@endsection
