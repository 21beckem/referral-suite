<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Push Notif</title>
</head>
<body>
  <h1>Javascript & PHP push notif demo</h1>
  <script>
navigator.serviceWorker.register("sw.js");

function enableNotif() {
  Notification.requestPermission().then((permission)=> {
    if (permission === 'granted') {
      // get service worker
      navigator.serviceWorker.ready.then((sw)=> {
        // subscribe
        sw.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: "BN2yWUCkOtoNVl5V9dwHj6rLYQn7-1YsnZw1Fpmc84hUtxKxd40JDC3XKw-uiByi95XXnZHhvTLAwd-lbtcpYvQ"
        }).then((subscription)=> {
          console.log(JSON.stringify(subscription));
        });
      });
    }
  });
}
  </script>
  <button onclick="enableNotif()">Enable Notif</button>
</body>
</html>