## How to incorporate this into your system

This is the "package" of data that Referral suite will receive and send: This type of data is called `JSON`
```json
{
  "overall_data": {
    "new_referrals": []
  },
  "area_specific_data": {
    "my_referrals": [],
    "follow_ups": [],
    "last_sync": ""
  },
  
  "changed_people": [],
  "claim_these": [],
  "follow_up_update": []
}
```
Here's what everything means:

## First off!
Whenever it says "person-array format" below, it means an array that could look like this:
(notes are written to show what each item means)
```json
[
  "Mormons Bok Request",          <-- Type of referral
  "98",                           <-- Id number. A unique number for this referral that the system uses to make edits to this person
  "2023-05-10T07:44:42+0000",     <-- Date timestamp of when the referral came in
  "Not sent",                     <-- Referral sent status
  "Hägersten",                    <-- Claimed area
  "",                             <-- Are this referral is in (usually blank until sent)
  "Yellow",                       <-- (not used) Area Book Status
  "Michael Becker",               <-- Full name
  "Michael",                      <-- First name
  "Becker",                       <-- Last name
  "1234567890",                   <-- Phone number
  "iLikeMissionaries@mail.com",   <-- Email
  "123 streety lane",             <-- Street Address
  "Stockholm",                    <-- City
  "12345",                        <-- Zip Code
  "svenska",                      <-- Preferred language
  "fb",                           <-- Referral origin, aka where it came from. Can be "fb", "ig", or "web"
  "MBR | MB | Blue with Lines",   <-- Ad name. So inboxers know what this person signed up for
  . . .                           <-- You can have a longer array than this but Referral Suite will ignore anything else at the end.
]
```


## `overall_data`

#### `new_referrals`
An array of new referrals that have not been claimed yet. Not in the "person-array format" listed above though. These are in a simplified format:
```json
[
  "Mormons Bok Request",       <-- Type of referral
  "2023-05-10T07:44:42+0000",  <-- Date timestamp of when the referral came in
  "Michael",                   <-- First name
  "Becker"                     <-- Last name
]
```

## `area_specific_data`

#### `my_referrals`
An array of all the people that this area has claimed in the person-array format

#### `follow_ups`
(THIS IS OPTIONAL! If you decide not to use this, just leave it as an empty array.)
An array of all the people this area needs to follow up on.

#### `last_sync`
A string that says when this area last synced their referral suite given as a timestamp.

## The following arrays items always be empty arrays!
Referral suite will populate these empty arrays with lists of what referrals' info has been changed in any way.

Your system needs to read the following arrays every time the user syncs. And if these arrays are not empty, your system needs to apply the changed info supplied. This changed people info will be in person-array format.

#### `changed_people`
When a referral is sent or set as not interested, that referrals person-array will be placed in here with the edited info.

#### `claim_these`
When a referral is claimed by an area, that referrals array will be placed in here. Remember this is not in person-array format, this is in the simplified format from the `new_referrals` section.

#### `follow_up_update`
(THIS IS ONLY APPLICABLE if you're using the `follow_ups` section above)
When a referral is marked as followed up, the status will be saved to the referral's person-array and listed here.



