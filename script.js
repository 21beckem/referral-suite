function inIframe() { try {return window.self !== window.top;} catch(e) {return true;} }
function redirectAfterFunction(el) {
    alert("doing functiony stuff");
    safeRedirect(el.getAttribute('href'));
}
function safeRedirect(ref) {
    if (!inIframe()) {
        window.location.href = ref;
        return;
    }
    window.parent.postMessage(JSON.stringify({
        type: 'redirect',
        data: ref,
    }), '*');
}
async function safeFetch(ref) {
    if (!inIframe()) {
        return await fetch(ref);
    }
    ref = window.parent.makeSafeLink(ref);
    // make link safe
    return await fetch(ref);
}
document.addEventListener('click', e => {
    const origin = e.target.closest('a');
    if (origin) {
        //console.log(origin);
        if (origin.hasAttribute("target")) {
            //alert('has target');
            return;
        }
        e.preventDefault();
        safeRedirect(origin.href);
    }
});
function setCookie(cname, cvalue) {
    localStorage.setItem(cname, cvalue);
}
function getCookie(cname) {
    return localStorage.getItem(cname);
}
function getCookieJSON(x) {
    let out = null;
    try {
        out = JSON.parse(getCookie(x));
    } catch (e) {}
    return out;
}
function setCookieJSON(x, y) {
    return setCookie(x, JSON.stringify(y));
}
function saveBeforeInfoPage(person, el) {
    setCookieJSON('linkPages', person);
    safeRedirect(el.getAttribute('href'));
}
function saveBeforeClaimPage(person, el) {
    setCookieJSON('linkPages', person);
    safeRedirect(el.getAttribute('href'));
}
function _(x) { return document.getElementById(x); }
/////   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #
/////  above functions make everything function with basic navigation between pages. No Touch!
/////   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #
let data = getCookieJSON('dataSync') || null;
let area = getCookie('areaUser') || null;

if (area == null) {
    safeRedirect('login.html');
}


async function SYNC(print=true, justRead=false) {
    if (area == null) {
        safeRedirect('login.html')
    }
    if (print) {
        _('loadingcover').style.display = '';
    }
    let fetchURL = 'https://script.google.com/macros/s/AKfycbxpzwbrmmhSO9rsMX10pVYPhFwKjYjhtlOarZZI9UnKTNx-u9fajBJNVkfBq3J-kvT7/exec?area=';
    fetchURL += area;
    fetchURL += (data == null || justRead) ? '' : '&data=' + encodeURIComponent( JSON.stringify(data) );
    console.log(fetchURL);
    const response = await fetch(fetchURL);
    const syncRes = await response.json();
    //alert('done');
    console.log(syncRes);

    //save to cookie
    setCookieJSON('dataSync', syncRes);

    //take away overlay
    if (print) {
        _('loadingcover').style.display = 'none';
    }
}

function makeListUNclaimedPeople() {
    const arr = data.overall_data.new_referrals;
    let output = '';
    for (let i = 0; i < arr.length; i++) {
        const per = arr[i];
        output += `<aa onclick="saveBeforeClaimPage(data.overall_data.new_referrals[` + i + `], this)" href="claim_the_referral.html">
          <div class="w3-bar" style="display: flex;">
            <div class="w3-bar-item w3-circle">
              <div class="w3-dot w3-left-align w3-circle" style="width:20px;height:20px; margin-top: 27px;"></div>
            </div>
            <div class="w3-bar-item">
              <span class="w3-large">` + per[2]  + `</span><br>
              <span>` + new Date(per[1]).toLocaleString() + `</span><br>
              <span>` + per[0] + `</span>
            </div>
          </div>
        </aa>`;
    }
    _('unclaimedlist').innerHTML = output;
}
function makeListClaimedPeople(arr) {
    let output = '';
    for (let i = 0; i < arr.length; i++) {
        const per = arr[i];
        output += `<aa onclick="saveBeforeInfoPage(data.area_specific_data.my_referrals[` + i + `], this)" href="contact_info.html">
          <div class="w3-bar" style="display: flex;">
            <div class="w3-bar-item w3-circle">
              <div class="w3-dot w3-left-align w3-circle" style="width:20px;height:20px; margin-top: 27px;"></div>
            </div>
            <div class="w3-bar-item">
              <span class="w3-large">` + per[5]  + `</span><br>
              <span>` + new Date(per[1]).toLocaleString() + `</span><br>
              <span>` + per[0] + `</span>
            </div>
          </div>
        </aa>`;
    }
    _('yourreferrals').innerHTML = output;
}
function fillInContactInfo() {
    const person = getCookieJSON('linkPages') || null;
    _('contactname').innerHTML = person[5];
    _('telnumber').href = 'tel:+' + person[8];
    //_('smsnumber').href = 'sms:+' + person[8];
    //_('emailcontact').href = 'https://docs.google.com/forms/d/e/1FAIpQLSefh5bdklMCAE-XKvq-eg1g7elYIA0Fudk-ypqLaDm0nO1EXA/viewform?usp=pp_url&entry.925114183=' + person[9] + '&entry.873933093=';

    _('referraltype').innerHTML = person[0];
    _('phonenumber').innerHTML = '+' + person[8];
    _('email').innerHTML = person[9];
    let addStr = person[10] + ' ' + person[11] + ' ' + person[12];
    _('address').innerHTML = addStr;
    _('googlemaps').href = 'http://maps.google.com/?q=' + encodeURI(addStr);
}
async function fillMessageExamples(requestType, folderName, pasteBox) {
    const person = getCookieJSON('linkPages') || null;
	const reqMssgUrl = 'templates/' + folderName + '/' + encodeURI(requestType) + '.txt';
	//console.log(reqMssgUrl);
	const rawFetch = await safeFetch(reqMssgUrl);
	const rawTxt = await rawFetch.text();

	text = rawTxt;
	const Messages = text.split(/\n{4,}/gm);
	console.log(Messages);
	let output = "";
	for (let i = 0; i < Messages.length; i++) {
		output += '<div class="w3-panel w3-card-subtle w3-light-grey w3-padding-16"><div class="googleMessage">' + Messages[i] + '</div><button onclick="send_' + folderName + '(this.previousSibling.innerHTML, \'' + person[8] + '\')" class="useThisTemplateBtn">Use This Template</button></div>';
	}
	pasteBox.innerHTML = output;
}
function send_sms(text, number) {
    const url = 'sms:' + encodeURI(String(number)) + '?body=' + encodeURI(text);
    console.log(url);
    safeRedirect(url);
}
function send_email(text, number) {
    const url = 'sms:' + encodeURI(String(number)) + '?body=' + encodeURI(text);
    console.log(url);
    safeRedirect(url);
}
function syncPageFillIn() {
    let syncDate = new Date(data.area_specific_data.last_sync);
    _('infobox').innerHTML = area + '<div class="w3-opacity">Last sync: ' + syncDate.toLocaleString() + '</div>';
}
function FORCEsyncPageFillIn() {
    _('infobox').innerHTML = area;
}
function syncButton(el) {
    SYNC().then(() => {
        safeRedirect(el.getAttribute('href'));
    });
}
function sendToAnotherArea() {
    const person = getCookieJSON('linkPages') || null;
    if (person == null) {
        alert('something went wrong. Try again');
        safeRedirect('index.html');
    }
    const newArea = document.getElementById('areadropdown').value;

    // set new area in data and save to cookie
    person[3] = 'Sent';
    person[4] = newArea;

    // overwrite old person
    let found = false;
    for (let i = 0; i < data.area_specific_data.my_referrals.length; i++) {
        const oldPer = data.area_specific_data.my_referrals[i];
        if (oldPer[1] == person[1]) {
            found = true;
            data.changed_people.push(person);
            setCookieJSON('dataSync', data);
            break;
        }
    }
    if (!found) {
        alert("something went wrong, we couldn't find this person. Try again");
        safeRedirect('index.html');
    }
    // send to force-sync.html
    safeRedirect('force-sync.html');
}
function deceasePerson() {
    const person = getCookieJSON('linkPages') || null;
    let youSure = confirm("Are you sure you want to decease this person? This cannot be undone");
    if (!youSure) {
        return;
    }
    if (person == null) {
        alert('something went wrong. Try again');
        safeRedirect('index.html');
    }

    // set new area in data and save to cookie
    person[3] = 'Not interested';

    // overwrite old person
    let found = false;
    for (let i = 0; i < data.area_specific_data.my_referrals.length; i++) {
        const oldPer = data.area_specific_data.my_referrals[i];
        if (oldPer[1] == person[1]) {
            found = true;
            data.changed_people.push(person);;
            setCookieJSON('dataSync', data);
            break;
        }
    }
    if (!found) {
        alert("something went wrong, we couldn't find this person. Try again");
        safeRedirect('index.html');
    }
    // send to force-sync.html
    safeRedirect('force-sync.html');
}
function claimPerson() {
    const person = getCookieJSON('linkPages') || null;
    if (person == null) {
        alert('something went wrong. Try again');
        safeRedirect('index.html');
        return;
    }
    if ( !("claim_these" in data) ) {
        data['claim_these'] = Array();
    }
    data['claim_these'].push(person);
    setCookieJSON('dataSync', data);
    // send to force-sync.html
    safeRedirect('force-sync.html');
}
function doubleCheckSignOut(el) {
    let youSure = confirm("Are you sure you want to log out? All unsynced changes will be lost");
    if (!youSure) {
        return;
    }
    safeRedirect(el.getAttribute('href'));
}
/////   #   #   #   #   #   #   #   #
/////     Stuff to do on every page
/////   #   #   #   #   #   #   #   #
window.onload = () => {
    try {
        _('reddot').style.display = (data.overall_data.new_referrals.length > 0) ? 'block' : 'none';
    } catch(e) {}
}
