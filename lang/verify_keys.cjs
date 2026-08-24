const fs = require('fs');
const tg = JSON.parse(fs.readFileSync('C:/OSPanel/home/Codemaster/lang/tg.json', 'utf8'));
const ru = JSON.parse(fs.readFileSync('C:/OSPanel/home/Codemaster/lang/ru.json', 'utf8'));

const tgPeer = Object.keys(tg).filter(k => k.startsWith('peer.') || k === 'peer_error');
const tgInterview = Object.keys(tg).filter(k => k.startsWith('interview_'));
const ruPeer = Object.keys(ru).filter(k => k.startsWith('peer.') || k === 'peer_error');
const ruInterview = Object.keys(ru).filter(k => k.startsWith('interview_'));

console.log('TG peer keys:', tgPeer.length);
console.log('TG interview keys:', tgInterview.length);
console.log('RU peer keys:', ruPeer.length);
console.log('RU interview keys:', ruInterview.length);
console.log('---');
console.log('Sample TG peer:', tgPeer[0], '=', tg[tgPeer[0]]);
console.log('Sample TG interview:', tgInterview[0], '=', tg[tgInterview[0]]);
console.log('Sample RU peer:', ruPeer[0], '=', ru[ruPeer[0]]);
console.log('Sample RU interview:', ruInterview[0], '=', ru[ruInterview[0]]);

// Verify JSON is valid
try { JSON.parse(fs.readFileSync('C:/OSPanel/home/Codemaster/lang/tg.json', 'utf8')); console.log('TG JSON: valid'); } catch(e) { console.log('TG JSON: INVALID', e.message); }
try { JSON.parse(fs.readFileSync('C:/OSPanel/home/Codemaster/lang/ru.json', 'utf8')); console.log('RU JSON: valid'); } catch(e) { console.log('RU JSON: INVALID', e.message); }
