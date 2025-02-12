/*
Give the service worker access to Firebase Messaging.
Note that you can only use Firebase Messaging here, other Firebase libraries are not available in the service worker.
*/
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
* New configuration for app@pulseservice.com
*/
firebase.initializeApp({
    apiKey: "AIzaSyDM5ZCt_ibX3EmgaSQAsaxykeEXgbWgf5w",
    authDomain: "mandegarpars2-9e7d9.firebaseapp.com",
    projectId: "mandegarpars2-9e7d9",
    storageBucket: "mandegarpars2-9e7d9.firebasestorage.app",
    messagingSenderId: "778812623421",
    appId: "1:778812623421:web:913b44ef07f2a7432eb854"
    // measurementId: "G-XXXXX"
});

/*
Retrieve an instance of Firebase Messaging so that it can handle background messages.
*/
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
    console.log(
        "[firebase-messaging-sw.js] Received background message ",
        payload,
    );
    /* Customize notification here */
    const notificationTitle = "Background Message Title";
    const notificationOptions = {
        body: "Background Message body.",
        icon: "https://mpsystem.ir/assets/media/image/logo.png",
    };

    return self.registration.showNotification(
        notificationTitle,
        notificationOptions,
    );
});
