# Referral Suite

Manage incoming referrals from social media ads before sending them to the mission's areas! But here's the best part: ANYONE can use it!

<br>

## Basic Overview
Referral Suite is broken up into 2 big parts: The site and the Client. The client technically isn't needed at all if using a computer, but if you want to use it on a Missionary Device which has MaaS360. Here's the link to the [Referral Suite Client](https://github.com/ssmission/referral-suite-client).


## How to Edit! 😁
There are tutorial videos for each step which can be found in the mission drive.

* [Add or remove area that will be inboxing](#Add-or-remove-area-that-will-be-inboxing)
* [Add or remove area to the whole mission](#Add-or-remove-area-to-the-whole-mission)
* [Edit Pre-written Template Messages](#Edit-Pre-written-Template-Messages)

____
### Add or remove area that will be inboxing

1. Open `login.html`
2. Find where it says:
```html
<!-- Claim button -->
<div id="arealoginbuttons" style="padding: 10%;">
...
```
3. Inside of this there's a list of areas. Each area is listed like this:
```html
<div class="w3-container w3-center" style="margin-top: 12.5%;">
    <button onclick="signInAsArea(this)" href="force-sync.html" class="w3-button w3-xlarge w3-round-large" style="width: 80%">Area Name</button>
</div>
```
* To add an area, copy the code above and paste it in with the list of areas
* OR To remove an area, find the codeblock for that area and remove it

* Link to video tutorial [here](link)

____
### Add or remove area to the whole mission

1. Open `referral_edit.html`
2. Find where it says:
```html
<select onchange="areaOptionChanged(this)" id="areadropdown">
...
```
3. Inside of this there's a list of every area in the mission. Each area is listed as such:
```html
<option>Area Name</option>
```
* To add an area, copy the code above and paste it in with the list of areas. __*keep areas in alphabetical order PLEASE*__
* OR To remove an area, find the codeblock for that area and remove it

* Link to video tutorial [here](link)

____
### Edit Pre-written Template Messages

1. Navigate to the `templates` folder
2. Open the `email` or `sms` folder, whichever you'd like to edit
3. Open the file for which you'd like to edit the prewritten templates
4. Edit/Paste-in/delete whichever you'd like.
5. __Keep In Mind:__ Referral Suite will separate each of these into individual texts __When there is at least 3 line breaks in between messages!__ For instance:

* This creates 1 message:
```txt
1| Hejsan jag heter Michael! Jag såg att du frågade efter en gratis Mormons Bok
2|
3|
4| Hur är det med dig?
```
<br>

* This creates 2 messages:
```txt
1| Hejsan jag heter Michael! Jag såg att du frågade efter en gratis Mormons Bok
2|
3|
4|
5| Hur är det med dig?
```


* Link to video tutorial [here](link)
