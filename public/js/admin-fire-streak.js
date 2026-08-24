function _admDrawStar(ctx,spikes,or,ir){
    var rot=Math.PI/2*3,step=Math.PI/spikes;
    ctx.beginPath();ctx.moveTo(0,-or);
    for(var i=0;i<spikes;i++){ctx.lineTo(Math.cos(rot)*or,Math.sin(rot)*or);rot+=step;ctx.lineTo(Math.cos(rot)*ir,Math.sin(rot)*ir);rot+=step}
    ctx.closePath();ctx.fill();
}
function triggerFireBurst(el,streak,level){
    var THEMES={
        ember:{label:'EMBER',cols:['#94a3b8','#cbd5e1','#64748b','#818cf8'],particles:30,fireworks:1,confetti:10,fanfare:'none',speed:4,glow:8},
        spark:{label:'SPARK',cols:['#7dd3fc','#93c5fd','#60a5fa','#818cf8'],particles:40,fireworks:2,confetti:15,fanfare:'none',speed:5,glow:10},
        warm:{label:'WARM',cols:['#facc15','#fbbf24','#f59e0b','#fb923c'],particles:50,fireworks:2,confetti:25,fanfare:'stars',speed:6,glow:14},
        hot:{label:'ON FIRE',cols:['#fb923c','#f97316','#ef4444','#fbbf24'],particles:60,fireworks:3,confetti:35,fanfare:'flames',speed:7,glow:18},
        super:{label:'SUPER',cols:['#f97316','#ef4444','#fb923c','#facc15'],particles:70,fireworks:3,confetti:40,fanfare:'rings',speed:8,glow:22},
        mega:{label:'MEGA',cols:['#ef4444','#dc2626','#f97316','#fbbf24'],particles:85,fireworks:4,confetti:50,fanfare:'burst',speed:9,glow:26},
        supernova:{label:'SUPERNOVA',cols:['#dc2626','#ef4444','#facc15','#f97316'],particles:100,fireworks:5,confetti:60,fanfare:'wave',speed:10,glow:30},
        inferno:{label:'INFERNO',cols:['#dc2626','#9333ea','#f97316','#facc15','#ef4444'],particles:120,fireworks:6,confetti:70,fanfare:'volcano',speed:11,glow:36},
        ascended:{label:'ASCENDED',cols:['#facc15','#fef08a','#fff','#fbbf24'],particles:140,fireworks:6,confetti:80,fanfare:'halo',speed:12,glow:40},
        immortal:{label:'IMMORTAL',cols:['#a855f7','#c084fc','#ec4899','#dc2626'],particles:160,fireworks:7,confetti:90,fanfare:'vortex',speed:13,glow:44},
        legendary:{label:'LEGENDARY',cols:['#ec4899','#f472b6','#f97316','#a855f7'],particles:180,fireworks:8,confetti:100,fanfare:'diamond',speed:14,glow:48},
        titan:{label:'TITAN',cols:['#22d3ee','#67e8f9','#a855f7','#f472b6','#c084fc'],particles:200,fireworks:9,confetti:110,fanfare:'lightning',speed:15,glow:52},
        eternal:{label:'ETERNAL',cols:['#ef4444','#a855f7','#22d3ee','#facc15','#ec4899','#f97316'],particles:240,fireworks:12,confetti:130,fanfare:'rainbow',speed:16,glow:60}
    };
    var t=THEMES[level]||THEMES.ember;
    var rect=el.getBoundingClientRect(),cx=rect.left+rect.width/2,cy=rect.top+rect.height/2;
    var W=window.innerWidth,H=window.innerHeight;
    var cv=document.createElement('canvas');
    cv.style.cssText='position:fixed;top:0;left:0;width:100vw;height:100vh;pointer-events:none;z-index:99999';
    cv.width=W;cv.height=H;document.body.appendChild(cv);
    var ctx=cv.getContext('2d'),t0=performance.now(),running=true;
    var pts=[];
    var baseSpd=t.speed||4;
    for(var i=0;i<t.particles;i++){
        var a=Math.PI*2*Math.random(),spd=baseSpd*(0.4+Math.random()*0.8);
        pts.push({x:cx,y:cy,vx:Math.cos(a)*spd,vy:Math.sin(a)*spd-3,life:1.5+Math.random()*2.5,age:0,size:2+Math.random()*5,color:t.cols[i%t.cols.length],trail:[]});
    }
    var fws=[],fwTimes=[];
    for(var f=0;f<t.fireworks;f++)fwTimes.push(800+f*600+Math.random()*400);
    var cfs=[];
    for(var i=0;i<t.confetti;i++){
        cfs.push({x:Math.random()*W,y:-20-Math.random()*H*0.5,w:4+Math.random()*6,h:8+Math.random()*10,color:t.cols[Math.floor(Math.random()*t.cols.length)],rot:Math.random()*6.28,rs:(Math.random()-0.5)*0.15,vx:(Math.random()-0.5)*2,vy:1.5+Math.random()*3,wb:Math.random()*6.28,ws:0.03+Math.random()*0.05,life:3+Math.random()*2,age:0});
    }
    function mkFW(){
        var x=Math.random()*W*0.6+W*0.2,ty=50+Math.random()*H*0.3,col=t.cols[Math.floor(Math.random()*t.cols.length)],sparks=[];
        for(var i=0;i<30+Math.random()*30;i++){
            var a=Math.PI*2*Math.random(),v=1.5+Math.random()*4;
            sparks.push({x:x,y:ty,vx:Math.cos(a)*v,vy:Math.sin(a)*v,life:1+Math.random()*1.5,age:0,size:1.5+Math.random()*2.5,color:Math.random()>0.5?col:t.cols[(i+1)%t.cols.length],trail:[]});
        }
        return{x:x,sy:H+10,ty:ty,age:0,dur:0.5+Math.random()*0.3,sparks:sparks,launched:false,col:col};
    }
    function drawFanfare(elapsed){
        var te=elapsed/1000;ctx.save();
        if(t.fanfare==='stars'){
            for(var i=0;i<8;i++){var a=(i/8)*Math.PI*2+te*0.5,r=60+Math.sin(te*3+i)*20;ctx.globalAlpha=0.6+Math.sin(te*4+i)*0.3;ctx.fillStyle=t.cols[i%t.cols.length];ctx.shadowColor=t.cols[i%t.cols.length];ctx.shadowBlur=8;ctx.translate(cx+Math.cos(a)*r,cy+Math.sin(a)*r);ctx.rotate(te*2+i);_admDrawStar(ctx,5,8,3);ctx.setTransform(1,0,0,1,0,0)}
        }else if(t.fanfare==='flames'){
            for(var i=0;i<12;i++){var a=(i/12)*Math.PI*2,r=50+Math.sin(te*5+i*0.5)*15,fs=6+Math.sin(te*6+i)*3;ctx.globalAlpha=0.7+Math.sin(te*4+i)*0.3;ctx.fillStyle=t.cols[i%t.cols.length];ctx.shadowColor=t.cols[i%t.cols.length];ctx.shadowBlur=12;ctx.beginPath();ctx.arc(cx+Math.cos(a)*r,cy+Math.sin(a)*r,fs,0,Math.PI*2);ctx.fill()}
        }else if(t.fanfare==='rings'){
            for(var r=0;r<4;r++){var rad=40+r*30+Math.sin(te*3+r)*10;ctx.globalAlpha=0.3+Math.sin(te*2+r)*0.2;ctx.strokeStyle=t.cols[r%t.cols.length];ctx.lineWidth=2;ctx.shadowColor=ctx.strokeStyle;ctx.shadowBlur=10;ctx.beginPath();ctx.arc(cx,cy,rad,0,Math.PI*2);ctx.stroke()}
        }else if(t.fanfare==='burst'){
            for(var i=0;i<6;i++){var a=(i/6)*Math.PI*2+te,r=30+te*40;ctx.globalAlpha=Math.max(0,0.8-te*0.3);ctx.fillStyle=t.cols[i%t.cols.length];ctx.shadowColor=ctx.fillStyle;ctx.shadowBlur=15;ctx.translate(cx+Math.cos(a)*r,cy+Math.sin(a)*r);ctx.rotate(te*3);_admDrawStar(ctx,4,10,4);ctx.setTransform(1,0,0,1,0,0)}
        }else if(t.fanfare==='wave'){
            for(var i=0;i<20;i++){var wx=cx+Math.cos(i*0.6+te*2)*(50+i*8),wy=cy+Math.sin(i*0.6+te*2)*(30+i*5);ctx.globalAlpha=Math.max(0,0.7-i*0.03);ctx.fillStyle=t.cols[i%t.cols.length];ctx.shadowColor=ctx.fillStyle;ctx.shadowBlur=8;ctx.beginPath();ctx.arc(wx,wy,3+Math.sin(te*4+i)*2,0,Math.PI*2);ctx.fill()}
        }else if(t.fanfare==='volcano'){
            for(var i=0;i<15;i++){var vx=cx+(Math.random()-0.5)*20,vy=cy-Math.random()*te*120;ctx.globalAlpha=Math.max(0,1-(cy-vy)/200);ctx.fillStyle=t.cols[i%t.cols.length];ctx.shadowColor=ctx.fillStyle;ctx.shadowBlur=10;ctx.beginPath();ctx.arc(vx,vy,3+Math.random()*4,0,Math.PI*2);ctx.fill()}
        }else if(t.fanfare==='halo'){
            ctx.globalAlpha=0.4+Math.sin(te*2)*0.3;ctx.strokeStyle='#facc15';ctx.lineWidth=3;ctx.shadowColor='#facc15';ctx.shadowBlur=20;ctx.beginPath();ctx.ellipse(cx,cy-10,40+Math.sin(te)*5,12,0,0,Math.PI*2);ctx.stroke();ctx.globalAlpha=0.2+Math.sin(te*3)*0.15;ctx.strokeStyle='#fef08a';ctx.lineWidth=2;ctx.beginPath();ctx.ellipse(cx,cy-10,55+Math.sin(te*1.5)*8,16,0,0,Math.PI*2);ctx.stroke()
        }else if(t.fanfare==='vortex'){
            for(var i=0;i<30;i++){var a=(i/30)*Math.PI*2+te*2,r=20+i*3+Math.sin(te+i*0.3)*10;ctx.globalAlpha=Math.max(0,0.7-i*0.02);ctx.fillStyle=t.cols[i%t.cols.length];ctx.shadowColor=ctx.fillStyle;ctx.shadowBlur=8;ctx.beginPath();ctx.arc(cx+Math.cos(a)*r,cy+Math.sin(a)*r,2.5,0,Math.PI*2);ctx.fill()}
        }else if(t.fanfare==='diamond'){
            for(var i=0;i<8;i++){var a=(i/8)*Math.PI*2+te*0.8,r=50+Math.sin(te*2+i)*15;ctx.globalAlpha=0.6+Math.sin(te*3+i)*0.3;ctx.fillStyle=t.cols[i%t.cols.length];ctx.shadowColor=ctx.fillStyle;ctx.shadowBlur=15;ctx.save();ctx.translate(cx+Math.cos(a)*r,cy+Math.sin(a)*r);ctx.rotate(te+i);ctx.beginPath();ctx.moveTo(0,-8);ctx.lineTo(6,0);ctx.lineTo(0,8);ctx.lineTo(-6,0);ctx.closePath();ctx.fill();ctx.restore()}
        }else if(t.fanfare==='lightning'){
            if(Math.sin(te*8)>0.7){ctx.globalAlpha=0.8;ctx.strokeStyle='#22d3ee';ctx.lineWidth=2;ctx.shadowColor='#22d3ee';ctx.shadowBlur=20;ctx.beginPath();var lx=cx,ly=cy;for(var s=0;s<5;s++){ctx.lineTo(lx,ly);lx+=(Math.random()-0.5)*40;ly+=20+Math.random()*15}ctx.stroke()}
            for(var i=0;i<5;i++){var mx=cx+Math.cos(te+i*1.3)*70,my=cy+Math.sin(te*0.7+i)*50;ctx.globalAlpha=0.5;ctx.fillStyle=t.cols[i%t.cols.length];ctx.shadowColor=ctx.fillStyle;ctx.shadowBlur=12;ctx.translate(mx,my);ctx.rotate(te*2);_admDrawStar(ctx,5,6,2);ctx.setTransform(1,0,0,1,0,0)}
        }else if(t.fanfare==='rainbow'){
            var rc=['#ef4444','#f97316','#facc15','#22c55e','#3b82f6','#a855f7'];
            for(var i=0;i<rc.length;i++){var r=30+i*15+Math.sin(te*2+i)*8;ctx.globalAlpha=0.25+Math.sin(te*3+i)*0.15;ctx.strokeStyle=rc[i];ctx.lineWidth=3;ctx.shadowColor=rc[i];ctx.shadowBlur=10;ctx.beginPath();ctx.arc(cx,cy,r,te+i*0.3,te+i*0.3+Math.PI*1.2);ctx.stroke()}
            for(var i=0;i<20;i++){var da=(i/20)*Math.PI*2+te,dr=80+Math.sin(te*2+i*0.5)*20;ctx.globalAlpha=0.4;ctx.fillStyle=rc[i%rc.length];ctx.shadowColor=ctx.fillStyle;ctx.shadowBlur=6;ctx.beginPath();ctx.arc(cx+Math.cos(da)*dr,cy+Math.sin(da)*dr,2,0,Math.PI*2);ctx.fill()}
        }
        ctx.restore();
    }
    function frame(now){
        if(!running)return;
        var el2=now-t0;ctx.clearRect(0,0,W,H);
        var fadeAlpha=1;if(el2>5000)fadeAlpha=Math.max(0,1-(el2-5000)/1500);
        ctx.globalAlpha=fadeAlpha;
        var alive=0;
        for(var i=0;i<pts.length;i++){
            var p=pts[i];p.age+=0.016;if(p.age>p.life)continue;alive++;
            p.x+=p.vx;p.y+=p.vy;p.vy+=0.06;p.vx*=0.99;
            p.trail.push({x:p.x,y:p.y});if(p.trail.length>5)p.trail.shift();
            var alpha=1-p.age/p.life,sz=p.size*(0.4+0.6*(1-p.age/p.life));
            for(var j=0;j<p.trail.length;j++){ctx.globalAlpha=alpha*(j/p.trail.length)*0.25;ctx.fillStyle=p.color;ctx.beginPath();ctx.arc(p.trail[j].x,p.trail[j].y,sz*0.3,0,Math.PI*2);ctx.fill()}
            ctx.save();ctx.globalAlpha=alpha;ctx.translate(p.x,p.y);ctx.shadowColor=p.color;ctx.shadowBlur=10;ctx.fillStyle=p.color;ctx.beginPath();ctx.arc(0,0,sz,0,Math.PI*2);ctx.fill();ctx.restore();
        }
        for(var f=0;f<fwTimes.length;f++){
            if(el2>=fwTimes[f]&&!fws[f])fws[f]=mkFW();
            var fw=fws[f];if(!fw)continue;
            if(!fw.launched){fw.age+=0.016;var pr=Math.min(fw.age/fw.dur,1),cy2=fw.sy+(fw.ty-fw.sy)*pr;ctx.globalAlpha=1;ctx.fillStyle=fw.col;ctx.shadowColor=fw.col;ctx.shadowBlur=8;ctx.beginPath();ctx.arc(fw.x,cy2,3,0,Math.PI*2);ctx.fill();ctx.globalAlpha=0.3;ctx.strokeStyle=fw.col;ctx.lineWidth=1;ctx.beginPath();ctx.moveTo(fw.x,cy2);ctx.lineTo(fw.x,cy2+15);ctx.stroke();if(pr>=1)fw.launched=true;alive++}
            else{for(var s=0;s<fw.sparks.length;s++){var sp=fw.sparks[s];sp.age+=0.016;if(sp.age>sp.life)continue;alive++;sp.x+=sp.vx;sp.y+=sp.vy;sp.vy+=0.04;sp.vx*=0.98;sp.trail.push({x:sp.x,y:sp.y});if(sp.trail.length>4)sp.trail.shift();var sa=1-sp.age/sp.life,ss=sp.size*(1-sp.age/sp.life*0.5);for(var j=0;j<sp.trail.length;j++){ctx.globalAlpha=sa*(j/sp.trail.length)*0.2;ctx.fillStyle=sp.color;ctx.beginPath();ctx.arc(sp.trail[j].x,sp.trail[j].y,ss*0.3,0,Math.PI*2);ctx.fill()}ctx.save();ctx.globalAlpha=sa;ctx.fillStyle=sp.color;ctx.shadowColor=sp.color;ctx.shadowBlur=6;ctx.beginPath();ctx.arc(sp.x,sp.y,ss,0,Math.PI*2);ctx.fill();ctx.restore()}}
        }
        for(var i=0;i<cfs.length;i++){
            var c=cfs[i];c.age+=0.016;if(c.age>c.life)continue;alive++;
            c.wb+=c.ws;c.x+=c.vx+Math.sin(c.wb)*1.5;c.y+=c.vy;c.rot+=c.rs;
            var ca=Math.max(0,1-c.age/c.life);
            ctx.save();ctx.globalAlpha=ca;ctx.translate(c.x,c.y);ctx.rotate(c.rot);ctx.fillStyle=c.color;ctx.shadowColor=c.color;ctx.shadowBlur=4;ctx.fillRect(-c.w/2,-c.h/2,c.w,c.h);ctx.restore();
        }
        drawFanfare(el2);
        ctx.globalAlpha=1;
        if(alive>0&&el2<7000)requestAnimationFrame(frame);else{running=false;cv.remove()}
    }
    requestAnimationFrame(frame);
    setTimeout(function(){running=false;cv.remove()},7500);
    el.style.transition='transform 0.15s cubic-bezier(0.175,0.885,0.32,1.275)';
    el.style.transform='scale(1.3)';
    setTimeout(function(){el.style.transform='scale(0.9)';setTimeout(function(){el.style.transform=''},120)},150);
    setTimeout(function(){running=false;cv.remove()},7500);
}

function firePreview() {
    return {
        days: 1,
        allLevels: [
            { name:'ember',      days:1,   range:'1-2',     icon:'fa-smog',           bg:'rgba(100,116,139,0.08)', color:'#94a3b8', border:'rgba(100,116,139,0.3)' },
            { name:'spark',      days:3,   range:'3-6',     icon:'fa-star',           bg:'rgba(56,189,248,0.06)', color:'#7dd3fc', border:'rgba(56,189,248,0.3)' },
            { name:'warm',       days:7,   range:'7-13',    icon:'fa-fire',           bg:'rgba(234,179,8,0.08)',  color:'#facc15', border:'rgba(234,179,8,0.35)' },
            { name:'hot',        days:14,  range:'14-29',   icon:'fa-fire',           bg:'rgba(249,115,22,0.1)',  color:'#fb923c', border:'rgba(249,115,22,0.4)' },
            { name:'super',      days:30,  range:'30-89',   icon:'fa-fire',           bg:'rgba(239,68,68,0.08)',  color:'#f97316', border:'rgba(249,115,22,0.5)' },
            { name:'mega',       days:90,  range:'90-179',  icon:'fa-fire-flame-curved',bg:'rgba(239,68,68,0.1)', color:'#ef4444', border:'rgba(239,68,68,0.45)' },
            { name:'supernova',  days:180, range:'180-364', icon:'fa-burst',          bg:'rgba(220,38,38,0.12)',  color:'#dc2626', border:'rgba(220,38,38,0.5)' },
            { name:'inferno',    days:365, range:'365-729', icon:'fa-volcano',        bg:'linear-gradient(135deg,rgba(220,38,38,0.2),rgba(147,51,234,0.2))', color:'#dc2626', border:'none' },
            { name:'ascended',   days:730, range:'2 years', icon:'fa-crown',          bg:'linear-gradient(135deg,rgba(250,204,21,0.15),rgba(255,255,255,0.08))', color:'#facc15', border:'none' },
            { name:'immortal',   days:1095,range:'3 years', icon:'fa-hat-wizard',     bg:'linear-gradient(135deg,rgba(139,92,246,0.2),rgba(220,38,38,0.1))', color:'#a855f7', border:'none' },
            { name:'legendary',  days:1460,range:'4 years', icon:'fa-shield-halved',  bg:'linear-gradient(135deg,rgba(236,72,153,0.2),rgba(249,115,22,0.15))', color:'#ec4899', border:'none' },
            { name:'titan',      days:1825,range:'5 years', icon:'fa-bolt-lightning', bg:'linear-gradient(135deg,rgba(34,211,238,0.15),rgba(168,85,247,0.2))', color:'#22d3ee', border:'none' },
            { name:'eternal',    days:2555,range:'7+ years',icon:'fa-circle-nodes',   bg:'linear-gradient(135deg,rgba(220,38,38,0.15),rgba(168,85,247,0.15),rgba(34,211,238,0.15),rgba(250,204,21,0.15))', color:'#f472b6', border:'none' },
        ],
        get levelName() {
            var d = parseInt(this.days);
            if (d >= 2555) return 'eternal';
            if (d >= 1825) return 'titan';
            if (d >= 1460) return 'legendary';
            if (d >= 1095) return 'immortal';
            if (d >= 730)  return 'ascended';
            if (d >= 365)  return 'inferno';
            if (d >= 180)  return 'supernova';
            if (d >= 90)   return 'mega';
            if (d >= 30)   return 'super';
            if (d >= 14)   return 'hot';
            if (d >= 7)    return 'warm';
            if (d >= 3)    return 'spark';
            return 'ember';
        },
        get iconClass() {
            var map = {ember:'fa-smog',spark:'fa-star',warm:'fa-fire',hot:'fa-fire',super:'fa-fire',mega:'fa-fire-flame-curved',supernova:'fa-burst',inferno:'fa-volcano',ascended:'fa-crown',immortal:'fa-hat-wizard',legendary:'fa-shield-halved',titan:'fa-bolt-lightning',eternal:'fa-circle-nodes'};
            return map[this.levelName] || 'fa-fire';
        },
        get levelCfg() {
            var l = this.allLevels.find(l => l.name === this.levelName);
            return l || this.allLevels[0];
        }
    };
}
