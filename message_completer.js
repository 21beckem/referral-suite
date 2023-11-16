const current_page = document.currentScript.getAttribute('current-page');
const person = idToReferral(getCookieJSON('linkPages')) || null;

const templateMssg = (getCookie('completeThisMessage'));

const ThingsToComplete = [...new Set( templateMssg.match(/{[^}]*}/gm) )];

//make a formated message ready to edit
let newTmpMssg = templateMssg;
let inputsOutput = '';
newTmpMssg = templateMssg.replace(/{[^}]*}/gm, function (x) {
	return '<span class="ljus completerViewThing_'+x.replace(' ', '_')+'">' + x + '</span>';
});
for (let i = 0; i < ThingsToComplete.length; i++) {
	const el = ThingsToComplete[i];
	//newTmpMssg = newTmpMssg.replace(el, '<span id="completerViewThing_'+String(el)+'" class="ljus">'+el+'</span>');
	elShort = el.substring(1, el.length - 1);
	inputsOutput += '<label for="' + elShort + '">' + elShort.toTitleCase() + ' </label><br><input id="completerInputFor_'+el+'" autocomplete="nope" oninput="updateField(this)" onfocus="focusThisFieldInMssg(this)" name="' + elShort + '" type="text"><br>';
}
newTmpMssg = newTmpMssg.replace(/(?:\r\n|\r|\n)/g, '<br>');
_('MessageOutput').innerHTML = newTmpMssg;
_('completerItemsParent').innerHTML = inputsOutput;

function focusThisFieldInMssg(el) {
	document.getElementsByClassName('ljus').forEach((el) => {
		el.classList.remove('selected');
	});
	document.getElementsByClassName('completerViewThing_{'+el.name.replace(' ', '_')+'}').forEach((x) => {
		x.classList.add('selected');
	});
}

function updateField(el) {
	document.getElementsByClassName('completerViewThing_{'+el.name.replace(' ', '_')+'}').forEach((x) => {
		x.innerHTML = (el.value == '') ? '{'+el.name+'}' : el.value;
	});
}

function sendTheMessage() {
	const link_beginning = (current_page == 'sms') ? 'sms:' + encodeURI(String(person[ TableColumns['phone'] ])) + '?body=' : 'https://docs.google.com/forms/d/e/1FAIpQLSefh5bdklMCAE-XKvq-eg1g7elYIA0Fudk-ypqLaDm0nO1EXA/viewform?usp=pp_url&entry.925114183=' + person[ TableColumns['email'] ] + '&entry.873933093=' + TEAM.email + '&entry.1947536680=';
	const sendLink = link_beginning + encodeURI(_('MessageOutput').innerText);
	_('fakeLinkToClickToSend').href = sendLink;
	let listOfIns = _('completerItemsParent').querySelectorAll('INPUT');
	let send = true;
	listOfIns.forEach(el => {
		if (el.value == "") {
			send = false;
		}
	});
	if (send) {
		logAttempt((current_page == 'sms') ? 1 : 2);
		localStorage.setItem('justAttemptedContact', '1');
		_('fakeLinkToClickToSend').click();
		safeRedirect('contact_info.php');
	} else {
		JSAlert.alert('Please fill out the required info 🙃');
	}
}

// put in some values we know of
function trySetValue(el, val) {
	try {
		el.value = val;
		updateField(el);
	} catch (e) {}
}
window.onload = () => {
	Object.keys(TableColumns).forEach((colName) => {
		console.log(person[ TableColumns[colName] ]);
		trySetValue(_('completerInputFor_{'+colName+'}'), String(person[ TableColumns[colName] ]).trim());
	});
	let addStr = person[TableColumns['street address']] + ' ' + person[TableColumns['city']] + ' ' + person[TableColumns['zip']];
	trySetValue(_('completerInputFor_{address}'), addStr);
}