const current_page = document.currentScript.getAttribute('current-page');
const person = getCookieJSON('linkPages') || null;

const templateMssg = (getCookie('completeThisMessage'));

const ThingsToComplete = [...new Set( templateMssg.match(/{[^}]*}/gm) )];
const MessageOutput = _('MessageOutput');

//make a formated message ready to edit
let newTmpMssg = templateMssg;
let inputsOutput = '';
newTmpMssg = templateMssg.replace(/{[^}]*}/gm, function (x) {
	return '<span class="ljus completerViewThing_'+x+'">' + x + '</span>';
});
for (let i = 0; i < ThingsToComplete.length; i++) {
	const el = ThingsToComplete[i];
	//newTmpMssg = newTmpMssg.replace(el, '<span id="completerViewThing_'+String(el)+'" class="ljus">'+el+'</span>');
	elShort = el.substring(1, el.length - 1);
	inputsOutput += '<label for="' + elShort + '">' + elShort + ' </label><br><input id="completerInputFor_'+el+'" autocomplete="nope" oninput="updateField(this)" onfocus="focusThisFieldInMssg(this)" name="' + elShort + '" type="text"><br>';
}
newTmpMssg = newTmpMssg.replace(/(?:\r\n|\r|\n)/g, '<br>');
MessageOutput.innerHTML = newTmpMssg;
_('completerItemsParent').innerHTML = inputsOutput;

function focusThisFieldInMssg(el) {
	let allCompleters = MessageOutput.querySelectorAll('.ljus');
	for (let i = 0; i < allCompleters.length; i++) {
		const e = allCompleters[i];
		e.classList.remove('selected');
	}
	document.getElementsByClassName('completerViewThing_{'+el.name+'}').forEach((x) => {
		x.classList.add('selected');
	});
}

function updateField(el) {
	document.getElementsByClassName('completerViewThing_{'+el.name+'}').forEach((x) => {
		x.innerHTML = (el.value == '') ? '{'+el.name+'}' : el.value;
	});
}

function sendTheMessage() {
	const link_beginning = (current_page == 'sms') ? 'sms:' + encodeURI(String(person[8])) + '?body=' : 'https://docs.google.com/forms/d/e/1FAIpQLSefh5bdklMCAE-XKvq-eg1g7elYIA0Fudk-ypqLaDm0nO1EXA/viewform?usp=pp_url&entry.925114183=' + person[9] + '&entry.873933093=' + getCookie('areaUserEmail') + '&entry.1947536680=';
	const sendLink = link_beginning + encodeURI(MessageOutput.innerText);
	_('fakeLinkToClickToSend').href = sendLink;
	let listOfIns = _('completerItemsParent').querySelectorAll('INPUT');
	let send = true;
	listOfIns.forEach(el => {
		if (el.value == "") {
			send = false;
		}
	});
	if (send) {
		_('fakeLinkToClickToSend').click();
	} else {
		alert('Please fill out the required info 🙃');
	}
}

// put in some values we know of
function trySetValue(el, val) {
	try {
		el.value = val;
		updateField(el);
	} catch (e) {}
}
trySetValue(_('completerInputFor_{Name}'), person[6]);
trySetValue(_('completerInputFor_{insert their number}'), person[8]);