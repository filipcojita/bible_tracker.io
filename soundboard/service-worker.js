const CACHE_NAME = "soundboard-v1";
const FILES_TO_CACHE = [
  "./",
  "./index.html",
  "./styles.css",
  "./script.js",
  "./manifest.json",
  "./icons/soundboard-icon.png",
  // Add sound files
  "./sounds/scene1/thunder.mp3",
  "./sounds/scene1/applause.mp3",
  "./sounds/scene2/boing.mp3",
  "./sounds/scene2/magic.mp3"
];

self.addEventListener("install", event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(FILES_TO_CACHE))
  );
});

self.addEventListener("fetch", event => {
  event.respondWith(
    caches.match(event.request).then(response => response || fetch(event.request))
  );
});
