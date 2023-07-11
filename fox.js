function didIJustContactEveryoneINeedToForToday() {
    let yes = true;
    for (let i = 0; i < data.area_specific_data.my_referrals.length; i++) {
        const per = data.area_specific_data.my_referrals[i];
        if (!hasPersonBeenContactedToday(per)) {
            yes = false;
            break;
        }
    }
    if (!yes) {
        data.fox.streak = getCookieJSON('unchangedSyncData').fox.streak;
        data.fox.lastStreakDay = getCookieJSON('unchangedSyncData').fox.lastStreakDay;
        setCookieJSON('dataSync', data);
        console.log('streak reset to last sync data');
        return;
    }
    // check if streak already done for today
    if (dateIsToday(new Date(data.fox.lastStreakDay))) {
        return;
    }
    // increase streak num and last day
    data.fox.streak += 1;
    const d = new Date();
    data.fox.lastStreakDay = d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate();
    setCookieJSON('dataSync', data);
    InboxFox.playAnimation('Wave1');
    InboxFox.say("Let's go!! Good job, you extended your streak!")
}
const dateIsToday = (someDate) => {
    if (String(someDate.getFullYear()).includes('1970')) {
        return false;
    }
    const today = new Date();
    return someDate.getDate() == today.getDate() &&
        someDate.getMonth() == today.getMonth() &&
        someDate.getFullYear() == today.getFullYear()
}
function handleDailyAndShiftlyNotifications() {

}