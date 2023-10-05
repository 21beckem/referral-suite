self.addEventListener("push", (event) => {
    const notification = event.data.json().notification;
    event.waitUntil(self.registration.showNotification(notification.title, {
        body: notification.body,
        icon: notification.image,
        
    }));
});

self.addEventListener("notificationclick", (event) => {
    event.waitUntil(clients.openWindow('/referral-suite-manager/unclaimed_referrals.php'));
});