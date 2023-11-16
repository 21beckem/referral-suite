let timesMarginBox = _('timesMarginBox');
let scheduleColDivs = _('scheduleColDivs');

let d = new Date();
let niceDate = d.toLocaleString("default", { year: "numeric" }) + "-" + d.toLocaleString("default", { month: "2-digit" }) + "-" + d.toLocaleString("default", { day: "2-digit" });
const iOfToday = schedArr.transpose()[0].indexOf(niceDate);

function scrollToToday() {
    scheduleColDivs.scrollTo(scheduleColDivs.offsetWidth*(iOfToday - 2), 0);
}
let teamColorLookup = {};
for (let i = 0; i < teamInfos.length; i++) {
    teamColorLookup[ teamInfos[i][0] ] = teamInfos[i][3];
}
function colorForTeam(teamId) {
    if (teamColorLookup.hasOwnProperty(teamId)) {
        return InboxColors[ teamColorLookup[teamId] ];
    } else { return ''; }
}

function writeSchedule() {
    const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    const dayNames = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

    let mainShOut = '';
    for (let i = 2; i < schedArr.length; i++) {
        const col = schedArr[i];
        let prevDay = (iOfToday>i) ? ' class="prevDayOrShift"' : '';
        mainShOut += '<div class="tableCol"><div'+prevDay+'>';

        // make date cell
        const thisDate = new Date(col[0]);
        const niceDate = dayNames[thisDate.getDay()] + '<br>' + monthNames[thisDate.getMonth()] + ' ' + String(thisDate.getDate());
        mainShOut += '<div>' + niceDate + '</div>';
        
        // create comparative date
        const d = new Date();
        const sDate = d.toLocaleString("default", { year: "numeric" }) + "-" + d.toLocaleString("default", { month: "2-digit" }) + "-" + d.toLocaleString("default", { day: "2-digit" });

        // make rest of cells
        for (let j = 1; j < col.length; j++) {
            const cell = col[j];
            
            // make dropdown list of all inboxers
            let inboxersOptions = '<option></option>';
            for (let k = 0; k < teamInfos.length; k++) {
                const team = teamInfos[k];
                inboxersOptions += '<option value="'+team[0]+'"'+( (cell==team[0]) ? ' selected' : '' )+'>' + team[1] + '</option>';
            }
            let prevShift = '';
            if (iOfToday==i) {
                //console.log(new Date(sDate+' '+schedArr[1][j]), d);
                if (new Date(sDate+' '+schedArr[1][j]).getTime() < d.getTime()) {
                    prevShift = ' class="prevDayOrShift"';
                }
            }

            mainShOut += '<div'+prevShift+' style="height:calc(99% / '+(col.length-1)+')"><select onchange="saveScheduleChange(this, '+i+', '+j+')" style="background-color: '+colorForTeam(cell)+';">' + inboxersOptions + '</select></div>';
        }

        mainShOut += '</div></div>';
    }
    scheduleColDivs.innerHTML = mainShOut;

    // times Col
    //timesMarginBox.innerHTML += '<div style="opacity:0">00:00-00:00</div>';
    for (let i = 1; i < schedArr[0].length; i++) {
        timesMarginBox.innerHTML += '<div>' + toLocalTimeFormat(schedArr[0][i]) + '<br>' + toLocalTimeFormat(schedArr[1][i]) + '</div>';
    }
    timesMarginBox.style.height = 'calc(100% - '+document.querySelector('#scheduleColDivs .tableCol > div > div').clientHeight+'px)';
}
function toLocalTimeFormat(tid) {
    return new Date('01/01/1970 '+tid).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}

async function saveScheduleChange(sel, x, y) {
    sel.style.filter = 'grayscale(0.8) brightness(1.2)';
    let formData = new URLSearchParams();
    formData.append('x', x);
    formData.append('y', y);
    formData.append('val', sel.value);
    const rawResponse = await fetch('php_functions/saveSchedule.php', {
        method: 'POST',
        body: formData
    });
    const content = await rawResponse.text();
    if (content=='success') {
        
        sel.style.backgroundColor = colorForTeam(sel.value);
    } else {
        JSAlert.alert('Oops! Something went wrong.<br>Please try again', '', JSAlert.Icons.Failed);
    }
    sel.style.filter = '';
}

window.addEventListener("load", (e) => {
    writeSchedule();
    scrollToToday();
});