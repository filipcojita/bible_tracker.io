const scenes = {
    scene1: [
      { label: "Chill Ambient", file: "sounds/scene1/chill-ambient.mpeg", id: "chill-ambient" },
      { label: "Scena1-ACTIUNE!", file: "sounds/scene1/motor-actiune.mp3", id: "motor-actiune" },
      { label: "ROSTOGOL", file: "sounds/scene1/rostogol.mp3", id: "rostogol" },
      { label: "Nu va temeti!", file: "sounds/scene1/nu-va-temeti.mp3", id: "nu-va-temeti" },
      { label: "Mergeti Repede!", file: "sounds/scene1/mergeti-repede.mp3", id: "mergeti-repede" },
      { label: "De ce cautati?", file: "sounds/scene1/de-ce-cautati.mp3", id: "de-ce-cautati" },
      { label: "Sad Music", file: "sounds/scene1/sad-music.mp3", id: "sad-music" }
    ],
    scene2: [
      { label: "Scena1-2-complet", file: "sounds/scene2/scena1-complet.mp3", id: "scena1-complet" },
      { label: "Chill Ambient", file: "sounds/scene1/chill-ambient.mpeg", id: "chill-ambient" },
      { label: "Inspirational Music", file:"sounds/scene2/insp-music.mp3", id: "insp-music"},
      { label: "De ce cautati?", file: "sounds/scene1/de-ce-cautati.mp3", id: "de-ce-cautati" }
    ]
  };
  
  const activeAudios = {};

  function switchScene(sceneKey) {
    document.querySelectorAll(".tab-button").forEach(btn => btn.classList.remove("active"));
    const index = Object.keys(scenes).indexOf(sceneKey);
    document.querySelectorAll(".tab-button")[index].classList.add("active");
  
    const soundboard = document.getElementById("soundboard");
    soundboard.innerHTML = "";
  
    scenes[sceneKey].forEach(sound => {
      const wrapper = document.createElement("div");
      wrapper.className = "sound-row";
  
      const label = document.createElement("div");
      label.textContent = sound.label;
      label.className = "sound-label";
  
      const volume = document.createElement("input");
      volume.type = "range";
      volume.min = 0;
      volume.max = 1;
      volume.step = 0.01;
      volume.value = 1;
  
      const loop = document.createElement("input");
      loop.type = "checkbox";
  
      const loopLabel = document.createElement("label");
      loopLabel.textContent = "🔁";
      loopLabel.style.marginLeft = "10px";
      loopLabel.appendChild(loop);
  
      const playBtn = document.createElement("button");
      playBtn.textContent = "▶️ Play";
      playBtn.className = "sound-button";
      playBtn.onclick = () => {
        stopSound(sound.id);
        const audio = new Audio(sound.file);
        audio.loop = loop.checked;
        audio.volume = parseFloat(volume.value);
        audio.play();
        activeAudios[sound.id] = audio;
      
        // Hook real-time volume control
        volume.addEventListener("input", () => {
          if (activeAudios[sound.id]) {
            activeAudios[sound.id].volume = parseFloat(volume.value);
          }
        });
      };
      
  
      const stopBtn = document.createElement("button");
      stopBtn.textContent = "⏹ Stop";
      stopBtn.className = "stop-button";
      stopBtn.onclick = () => stopSound(sound.id);
  
      const controlsRow = document.createElement("div");
      controlsRow.className = "sound-controls";
      controlsRow.appendChild(volume);
      controlsRow.appendChild(loopLabel);
  
      wrapper.appendChild(label);
      wrapper.appendChild(controlsRow);
      wrapper.appendChild(playBtn);
      wrapper.appendChild(stopBtn);
  
      soundboard.appendChild(wrapper);
    });
  }
  
  function stopSound(id) {
    if (activeAudios[id]) {
      activeAudios[id].pause();
      activeAudios[id].currentTime = 0;
      delete activeAudios[id];
    }
  }
  
  function stopAllSounds() {
    Object.keys(activeAudios).forEach(stopSound);
  }
  
  // Load default scene
  switchScene("scene1");