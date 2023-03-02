<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />


<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.3.2/axios.min.js" integrity="sha512-NCiXRSV460cHD9ClGDrTbTaw0muWUBf/zB/yLzJavRsPNUl9ODkUVmUHsZtKu17XknhsGlmyVoJxLg/ZQQEeGA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script type="text/javascript" src="https://unpkg.com/@speechly/browser-ui/core/push-to-talk-button.js"></script>
<script type="text/javascript" src="https://unpkg.com/@speechly/browser-ui/core/transcript-drawer.js"></script>
<script type="text/javascript" src="https://unpkg.com/@speechly/browser-ui/core/intro-popup.js"></script>
<script src="https://code.responsivevoice.org/responsivevoice.js?key=1C1Gm7Yj"></script>

<body onload="init()">
    <div id="skynet-chat-box">
        <transcript-drawer hint='Try: "Try add 5 and 9"' placement="top">
        </transcript-drawer>

        <div id="chat-control-div">
            <div id="skynet_msg">
                <div id="robot">
                    <div class="skynet_chat_profile_head">
                        <div class="bot"></div>
                        <h3 style='font-family: monospace; font-size:x-large;color:white'><strong>Skynet</strong></h3>
                    </div>

                    <button id="voice-button" class="enabled">
                        <i class="fas fa-volume-up"></i>
                    </button>
                </div>
            </div>

            <div class="skynet_input">
                <input type="text" style="font-family: monospace;" id="skynet_msg_send" placeholder="New Message">
                <push-to-talk-button intro='Try: "Try multiply 7 by 9"' borderscale="0" appid="c9f51b42-626c-4557-93a9-93cb205141d9" taptotalktime: 0, holdscale="1.1" , iconsize="40%" , fxsize="150%" size="40px" iconcolor="white" backgroundcolor="#1B365D">
                </push-to-talk-button>
                <skynet-button style='width: 40px' class="skynet_send" id="reply"><i class="fas fa-paper-plane"></i></skynet-button>
            </div>
        </div>
        <intro-popup>
        </intro-popup>
    </div>

    <skynet-button id="toggle-chat">
        <i class="fas fa-comment-alt"></i>
    </skynet-button>


</body>

<script>
    <?php echo "const host='" . get_option("local_python_server_host", "http://localhost:6929") . "';"; ?>
    const toggleChat = document.getElementById("toggle-chat");
    const chatBox = document.getElementById("skynet-chat-box");
    const skynet_msg = document.getElementById("skynet_msg");
    const pastMessages = [{
        "role": "system",
        "content": "You are a chat bot. Your name is skynet. You are created by devsabi. And you are currently used in skynet-chat plugin."
    }]

    // get a reference to the voice-button element
    const voiceButton = document.getElementById("voice-button");

    // initialize the isVoiceRequired flag to false
    let isVoiceRequired = true;

    // add an event listener to toggle the isVoiceRequired flag
    voiceButton.addEventListener("click", function() {
        isVoiceRequired = !isVoiceRequired;
        console.log(`isVoiceRequired: ${isVoiceRequired}`);

        // toggle the enabled and disabled classes on the voice-button element
        voiceButton.classList.toggle("enabled");
        voiceButton.classList.toggle("disabled");

        // update the Font Awesome icon
        const iconElement = voiceButton.querySelector("i");
        if (isVoiceRequired) {
            iconElement.classList.remove("fa-volume-mute");
            iconElement.classList.add("fa-volume-up");
        } else {
            iconElement.classList.remove("fa-volume-up");
            iconElement.classList.add("fa-volume-mute");
        }
    });


    toggleChat.addEventListener("click", function() {
        chatBox.classList.toggle("expanded");
        skynet_msg.scrollTop = skynet_msg.scrollHeight; /* scroll to bottom when expanding */
    });


    document
        .getElementsByTagName("push-to-talk-button")[0]
        .addEventListener("speechsegment", (e) => {
            const segment = e.detail;

            if (segment.isFinal) {
                let transcript = segment.words
                    .map(w => w.value.toLowerCase())
                    .join(" ");
                // Add trailing period upon segment end.
                if (segment.isFinal) transcript += ".";
                console.log(transcript)
                console.log("speechsegment message:", segment);
                // Optionally show confirmation checkmark
                window.postMessage({
                    type: "speechhandled",
                    success: true,
                    transcript: transcript
                }, "*");
            }
        });

    window.addEventListener("message", function(event) {
        if (event.data.type === "speechhandled") {
            if (event.data.success) {
                // Get the transcription from the event.data.transcription property
                document.getElementById("skynet_msg_send").value = event.data.transcript;
                document.getElementById('reply').click();
                const transcription = event.data.transcript;
                console.log("Transcription:", transcription);
            } else {
                console.error("Speech handling failed");
            }
        }
    });


    function init() {
        let res_elm = document.createElement("div");
        res_elm.innerHTML = "Hello Myself Skynet";
        res_elm.setAttribute("class", "skynet_msg_left");

        document.getElementById('skynet_msg').appendChild(res_elm);
    }

    function speakWithSynthesis(text, female = true) {
        // get a list of available voices
        let voices = speechSynthesis.getVoices();

        // find a British female voice, or use a default voice
        let voice;
        if (female) {
            voice = voices.find(v => v.lang === 'en-GB');
            if (!voice) {
                voice = voices.find(v => v.lang === 'en-US');
            }
        } else {
            voice = voices[0]
        }

        // create a new SpeechSynthesisUtterance object
        const utterance = new SpeechSynthesisUtterance();

        // set the text to be spoken
        utterance.text = text;

        // set the voice to the selected voice
        utterance.voice = voice;

        // set the pitch and rate of the voice (optional)
        utterance.pitch = 1;
        utterance.rate = 1;

        // speak the text
        window.speechSynthesis.speak(utterance);
    }

    function textToSpeech(text) {
        if (!isVoiceRequired) return
        // check if the browser supports the SpeechSynthesis API
        if ('speechSynthesis' in window) {
            speakWithSynthesis(text)
        } else {
            try {
                responsiveVoice.speak(text, "UK English Female", {
                    pitch: 1,
                    rate: 1
                });
            } catch {
                speakWithSynthesis(text, false)
            }

        }
    }

    document.getElementById('reply').addEventListener("click", async (e) => {
        e.preventDefault();

        var req = document.getElementById('skynet_msg_send').value;
        var bodyFormData = new FormData()
        bodyFormData.append("text", req)
        var req_json = {
            "pastMessages": pastMessages,
            "text": req
        }

        if (req == undefined || req == "") {

        } else {

            let data_req = document.createElement('div');
            let data_res = document.createElement('div');

            let container1 = document.createElement('div');
            let container2 = document.createElement('div');

            container1.setAttribute("class", "skynet_msgCon1");
            container2.setAttribute("class", "skynet_msgCon2");

            data_req.innerHTML = req;
            data_res.innerHTML = 'Generating the response....';


            data_req.setAttribute("class", "skynet_msg_right");
            data_res.setAttribute("class", "skynet_msg_left");

            let message = document.getElementById('skynet_msg');


            message.appendChild(container1);
            message.appendChild(container2);

            container1.appendChild(data_req);
            container2.appendChild(data_res);

            document.getElementById('skynet_msg_send').value = "";

            function scroll() {
                var scrollMsg = document.getElementById('skynet_msg')
                scrollMsg.scrollTop = scrollMsg.scrollHeight;
            }
            scroll();
            pastMessages.push({
                "role": "user",
                "content": req
            })

            var res = "";
            await axios({
                method: "post",
                url: host + "/api/generate_response",
                data: req_json,
                headers: {
                    "Content-Type": "application/json"
                }
            }).then(data => {
                res = JSON.stringify(data.data.message).slice(1, -1)
                pastMessages.push({
                    "role": "assistant",
                    "content": res
                })

            }).catch(data => {
                pastMessages.pop()
                console.log(data)
                if (data.status != 200) {
                    res = "Sorry, There was a problem. I think links to my python powered brain is severed."
                }
            })

            let escapeToHtml = (str) => {
                const htmlEscapes = {
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#39;",
                    "/": "&#x2F;",
                    '\\"': "\""
                };

                return str.replace(/[&<>"'\\\/]|\\n/g, function(match) {
                    return htmlEscapes[match] || "<br>";
                });
            };

            data_res.innerHTML = escapeToHtml(res);
            textToSpeech(data_res.textContent)
            scroll();
        }
    });

    document.getElementById('skynet_msg_send').addEventListener("keydown", async (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('reply').click();
        }
    });
</script>