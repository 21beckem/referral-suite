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
document.addEventListener('click', e => {
    const origin = e.target.closest('a');
    if (origin) {
        e.preventDefault();
        safeRedirect(origin.href);
    }
});
function setCookie(cname, cvalue, exdays='') {
    let expires = '';
    if (exdays != '') {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        expires = ";expires="+d.toUTCString();
    }
    document.cookie = cname + "=" + cvalue + expires + ";path=/";
}
function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
function _(x) { return document.getElementById(x); }
/////   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #
/////  above functions make everything function with basic navigation between pages. No Touch!
/////   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #   #



async function SYNC(justRead=false) {
    if (area == null) {
        safeRedirect('login.html')
    }
    _('loadingcover').style.display = '';
    let fetchURL = 'https://script.google.com/macros/s/AKfycbz5sdUaBw3uIK-TdMwxRgPcTTwDTT3PlVqlJZMVgqm8XIiraTtZRp_RySREpgx6aY4R/exec?area=';
    fetchURL += area;
    fetchURL += (data == null || justRead) ? '' : '&data=' + JSON.stringify(data);
    const response = await fetch(fetchURL);
    const syncRes = await response.json();
    alert('done');
    console.log(syncRes);

    //save to cookie
    setCookie('dataSync', JSON.stringify(syncRes));

    //take away overlay
    _('loadingcover').style.display = 'none';
}

function makeListUNclaimedPeople() {
    const arr = data.overall_data.new_referrals;
    let output = '';
    for (let i = 0; i < arr.length; i++) {
        const per = arr[i];
        output += `<a onclick="redirectAfterFunction(this)" href="claim_the_referral.html">
          <li class="w3-bar" style="display: flex">
            <div class="w3-bar-item w3-circle" style="width:65px;height:65px">
              <div class="w3-margin-top w3-left-align w3-dot w3-circle" style="width:20px;height:20px"></div>
            </div>
            <div class="w3-bar-item">
              <span class="w3-large w3-ellipsis">` + per[2] + `</span><br>
              <span>` + new Date(per[1]).toDateString() + `</span>
            </div>
          </li>
          </a>`;
    }
    //_('output').innerHTML = output;
    return output;
}
function makeListClaimedPeople() {
    const arr = data.area_specific_data.my_referrals;
    let output = '';
    for (let i = 0; i < arr.length; i++) {
        const per = arr[i];
        output += `<a onclick="redirectAfterFunction(this)" href="contact_info.html">
        <li class="w3-bar" style="display: flex">
          <div class="w3-bar-item w3-circle" style="width:65px;height:65px">
            <div class="w3-margin-top w3-left-align w3-dot w3-circle" style="width:20px;height:20px"></div>
          </div>
          <div class="w3-bar-item">
            <span class="w3-large w3-ellipsis">` + per[5]  + `</span><br>
            <span>` + new Date(per[1]).toDateString() + `</span>
          </div>
        </li>
        </a>`;
    }
    //_('output').innerHTML = output;
    return output;
}




/////   #   #   #   #   #   #   #   #
/////     Stuff to do on every page
/////   #   #   #   #   #   #   #   #
let data, area = null;
window.onload = () => {
    data = getCookie('dataSync') || null;
    if (data != null) { data = JSON.parse(data); }
    area = getCookie('areaUser') || null;
    _('reddot').style.display = (data.overall_data.new_referrals.length > 0) ? 'block' : 'none';
}
