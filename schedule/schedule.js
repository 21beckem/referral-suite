let timesContainer = _('timesContainer');
let scheduleColDivs = _('scheduleColDivs');

function scrollToToday(data, win, numberOfHiddenCols) {
    const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    const dayNames = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    let niceDate = dayNames[new Date().getDay()] + '\n' + monthNames[new Date().getMonth()] + ' ' + String(new Date().getDate());
    let iOfToday = data[0].indexOf(niceDate) - numberOfHiddenCols;
    win.scrollTo(win.offsetWidth*iOfToday, 0);
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

function writeTable() {
    const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    const dayNames = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    let mainTbOut = '<tr>';
    for (let i = 2; i < schedArr[0].length; i++) {
        const thisDate = new Date(schedArr[0][i]);
        const niceDate = dayNames[thisDate.getDay()] + '<br>' + monthNames[thisDate.getMonth()] + ' ' + String(thisDate.getDate());
        mainTbOut += '<td>' + niceDate + '<td>';
    }
    mainTbOut += '</tr>';


    // make HTML table (main table)
    for (let i = 1; i < schedArr.length; i++) {
        const row = schedArr[i];
        mainTbOut += '<tr>';
        for (let j = 2; j < row.length; j++) {
            const cell = row[j];
            // make dropdown list of all inboxers
            let inboxersOptions = '<option></option>';
            for (let k = 0; k < teamInfos.length; k++) {
                const team = teamInfos[k];
                inboxersOptions += '<option value="'+team[0]+'"'+( (cell==team[0]) ? ' selected' : '' )+'>' + team[1] + '</option>';
            }

            mainTbOut += '<td><select onchange="dropdownOnChange(this, '+i+', '+j+')" style="background-color: '+colorForTeam(cell)+';">' + inboxersOptions + '</select></td>';
        }
        mainTbOut += '</tr>';
    }
    scheduleColDivs.innerHTML = mainTbOut;


    let mainShOut = '';
    for (let i = 2; i < schedArr.length; i++) {
        const col = schedArr[i];
        mainShOut += '<div class="tableCol"><div>';

        // make date cell
        const thisDate = new Date(schedArr[0][i]);
        const niceDate = dayNames[thisDate.getDay()] + '<br>' + monthNames[thisDate.getMonth()] + ' ' + String(thisDate.getDate());
        mainTbOut += '<div>' + niceDate + '<div>';

        // make rest of cells
        for (let j = 1; j < col.length; j++) {
            const cell = col[j];
            
            // make dropdown list of all inboxers
            let inboxersOptions = '<option></option>';
            for (let k = 0; k < teamInfos.length; k++) {
                const team = teamInfos[k];
                inboxersOptions += '<option value="'+team[0]+'"'+( (cell==team[0]) ? ' selected' : '' )+'>' + team[1] + '</option>';
            }

            mainShOut += '<div><select onchange="dropdownOnChange(this, '+i+', '+j+')" style="background-color: '+colorForTeam(cell)+';">' + inboxersOptions + '</select></div>';
        }

        mainShOut += '</div></div>';
    }
}

window.addEventListener("load", (e) => {
    // SheetMap.load();
    // const colsToHide = [0,1];
    // _('scheduleParent').innerHTML += SheetMap.makeColDivsHTML(hideC=colsToHide);
    // let timesContainer = _('timesContainer');
    // timesContainer.innerHTML += '<div></div>';
    // for (let i = 1; i < SheetMap.vars.tableDataNOW.length; i++) {
    //     timesContainer.innerHTML += '<div>' + SheetMap.vars.tableDataNOW[i][0] + '-' + SheetMap.vars.tableDataNOW[i][1] + '</div>';
    // }
    // scrollToToday(SheetMap.vars.tableDataNOW, _('SheetMapColDivs'), colsToHide.length);
});