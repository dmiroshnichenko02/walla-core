document.addEventListener('DOMContentLoaded', function () {
    const ajax = window.tutor_ajax_object || {};
    const ajaxUrl = ajax.ajax_url || '/wp-admin/admin-ajax.php';
    const nonce = ajax.nonce || '';

    function postToggle(action, idKey, idValue, checkbox) {
        const body = new URLSearchParams();
        body.append('action', action);
        body.append(idKey, idValue);
        body.append('completed', checkbox.checked ? 1 : 0);
        body.append('nonce', nonce);

        return fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString()
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                alert(data.data?.message || 'Fail dave mark');
                checkbox.checked = !checkbox.checked;
                if (checkbox.checked) {
                    checkbox.setAttribute('checked', 'checked');
                } else {
                    checkbox.removeAttribute('checked');
                }
            } else if (checkbox.classList.contains('tutor-lesson-toggle')) {
                location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            checkbox.checked = !checkbox.checked;
            if (checkbox.checked) {
                checkbox.setAttribute('checked', 'checked');
            } else {
                checkbox.removeAttribute('checked');
            }
        });
    }

    function setupToggle(selector, action, idAttrName, idKey) {
        document.querySelectorAll(selector).forEach(el => {
            el.addEventListener('click', function (e) {
                e.stopPropagation();
            });

            el.addEventListener('change', function (e) {
                e.stopPropagation();
                const idValue = this.dataset[idAttrName];
                if (this.checked) {
                    this.setAttribute('checked', 'checked');
                } else {
                    this.removeAttribute('checked');
                }
                postToggle(action, idKey, idValue, this);
            });
        });
    }

    // data-lesson-id -> action tutor_toggle_lesson
    setupToggle('.tutor-lesson-toggle', 'tutor_toggle_lesson', 'lessonId', 'lesson_id');

    // data-quiz-id -> action tutor_toggle_quiz
    setupToggle('.tutor-quiz-toggle', 'tutor_toggle_quiz', 'quizId', 'quiz_id');

});

// Paste to your lesson-toggle.js (or inline in footer after tutor_ajax_object is defined)
document.addEventListener('DOMContentLoaded', function () {
  const ajaxObj = window.tutor_ajax_object || {};
  const ajaxUrl = ajaxObj.ajax_url || '/wp-admin/admin-ajax.php';
  const nonce = ajaxObj.nonce || '';

  // read tutor json from template
  const infoEl = document.getElementById('tutor_video_tracking_information');
  if (!infoEl) {
    // console.warn('tutor_video_tracking_information not found — nothing to track.');
    return;
  }

  let trackInfo;
  try {
    trackInfo = JSON.parse(infoEl.value);
  } catch (e) {
    // console.warn('Invalid JSON in tutor_video_tracking_information', e);
    return;
  }

  const postId = trackInfo.post_id || trackInfo.postId || null;
  const requiredPct = (trackInfo.required_percentage || 80) / 100;
  const configuredDuration = Number(trackInfo.video_duration || 0); 
  const defaultDurationFallback = 300; 

  // if (!postId) console.warn('Lesson post_id not found in tutor_video_tracking_information.');

  let alreadyMarked = false;
  async function markLessonCompleteOnce() {
    if (alreadyMarked || !postId) return;
    alreadyMarked = true;
    try {
      const res = await fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'tutor_toggle_lesson',
          lesson_id: postId,
          completed: 1,
          nonce: nonce
        })
      });
      const data = await res.json();
      if (data && data.success) {
        // console.log('✅ Lesson marked completed (ajax):', postId);
        const lessonRow = document.querySelector(`[data-lesson-id="${postId}"]`);
        const checkbox = lessonRow ? lessonRow.querySelector('.tutor-lesson-toggle') : null;
        if (checkbox) {
          checkbox.checked = true;
          checkbox.classList.add('tutor-lesson-completed');
        }
      } else {
        // console.warn('Tutor AJAX returned error', data);
      }
    } catch (err) {
      console.error('AJAX error marking lesson complete', err);
    }
  }

  // ---------------- HTML5 video handler ----------------
  function handleHTML5(video) {
    if (video.dataset.tutorTracked) return;
    video.dataset.tutorTracked = '1';
    const thresholdFunc = () => {
      const dur = video.duration || configuredDuration || 0;
      if (!dur || isNaN(dur)) return;
      if ((video.currentTime / dur) >= requiredPct) {
        markLessonCompleteOnce();
      }
    };
    video.addEventListener('timeupdate', thresholdFunc);
    // extra timer (in case timeupdate rare)
    const interval = setInterval(() => {
      if (video.paused && video.readyState === 0) return;
      thresholdFunc();
      if (alreadyMarked) clearInterval(interval);
    }, 1500);
  }

  // ---------------- YouTube iframe handler ----------------
  function loadYouTubeAPI(cb) {
    if (window.YT && window.YT.Player) return cb();
    if (document.querySelector('script[src*="youtube.com/iframe_api"]')) {
      const prev = window.onYouTubeIframeAPIReady;
      window.onYouTubeIframeAPIReady = function () {
        if (typeof prev === 'function') prev();
        cb();
      };
      return;
    }
    const tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    document.head.appendChild(tag);
    window.onYouTubeIframeAPIReady = cb;
  }
  function handleYouTubeIframe(iframe) {
    if (iframe.dataset.tutorTracked) return;
    iframe.dataset.tutorTracked = '1';
    loadYouTubeAPI(() => {
      try {
        const player = new YT.Player(iframe, {
          events: {
            onStateChange: function (ev) {
              if (ev.data === YT.PlayerState.PLAYING) {
                const poll = setInterval(() => {
                  try {
                    const dur = player.getDuration() || configuredDuration || 0;
                    const cur = player.getCurrentTime() || 0;
                    if (dur && cur / dur >= requiredPct) {
                      clearInterval(poll);
                      markLessonCompleteOnce();
                    }
                    if (player.getPlayerState() === YT.PlayerState.ENDED) clearInterval(poll);
                    if (alreadyMarked) clearInterval(poll);
                  } catch (e) {
                    clearInterval(poll);
                  }
                }, 1500);
              }
            }
          }
        });
      } catch (e) {
        // console.warn('YouTube player init failed', e);
      }
    });
  }

  // ---------------- Vimeo iframe handler ----------------
  function loadVimeoAPI(cb) {
    if (window.Vimeo && window.Vimeo.Player) return cb();
    if (document.querySelector('script[src*="player.vimeo.com/api/player.js"]')) {
      const check = setInterval(() => {
        if (window.Vimeo && window.Vimeo.Player) {
          clearInterval(check);
          cb();
        }
      }, 200);
      return;
    }
    const tag = document.createElement('script');
    tag.src = 'https://player.vimeo.com/api/player.js';
    tag.onload = cb;
    document.head.appendChild(tag);
  }
  function handleVimeoIframe(iframe) {
    if (iframe.dataset.tutorTracked) return;
    iframe.dataset.tutorTracked = '1';
    loadVimeoAPI(() => {
      try {
        const player = new Vimeo.Player(iframe);
        player.on('timeupdate', data => {
          const dur = data.duration || configuredDuration || 0;
          const cur = data.seconds || 0;
          if (dur && cur / dur >= requiredPct) {
            markLessonCompleteOnce();
          }
        });
      } catch (e) {
        // console.warn('Vimeo player init failed', e);
      }
    });
  }

  // ---------------- Generic iframe fallback ----------------
  function handleGenericIframe(iframe) {
    if (iframe.dataset.tutorTracked) return;
    iframe.dataset.tutorTracked = '1';
    const wrapper = iframe.closest('.tutor-video-player-wrapper') || iframe.parentElement;
    const duration = configuredDuration || defaultDurationFallback;
    const needed = duration * requiredPct;
    let watched = 0;
    let counting = false;
    let interval = null;
    function startCounting() {
      if (counting || alreadyMarked) return;
      counting = true;
      interval = setInterval(() => {
        if (document.hidden) return;
        watched += 1;
        if (watched >= needed) {
          clearInterval(interval);
          markLessonCompleteOnce();
        }
      }, 1000);
    }
    function stopCounting() {
      if (!counting) return;
      counting = false;
      if (interval) clearInterval(interval);
    }
    ['click', 'touchstart', 'pointerdown'].forEach(evt => {
      wrapper.addEventListener(evt, startCounting, { passive: true });
    });
    iframe.addEventListener('load', () => {
    });
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) stopCounting();
      else startCounting();
    });
    window.addEventListener('beforeunload', () => {
      if (interval) clearInterval(interval);
    });
  }

  // ---------------- init detection ----------------
  function initPlayers() {
    const videos = document.querySelectorAll('.tutor-video-player-wrapper video');
    if (videos.length) {
      videos.forEach(handleHTML5);
    }

    const iframes = document.querySelectorAll('.tutor-video-player-wrapper iframe');
    iframes.forEach(iframe => {
      const src = iframe.src || '';
      if (/youtube\.com\/embed|youtu\.be/.test(src)) {
        handleYouTubeIframe(iframe);
      } else if (/player\.vimeo\.com/.test(src)) {
        handleVimeoIframe(iframe);
      } else {
        handleGenericIframe(iframe);
      }
    });
  }

  initPlayers();

  const observer = new MutationObserver((mutations) => {
    clearTimeout(window.__tutor_video_init_timer);
    window.__tutor_video_init_timer = setTimeout(initPlayers, 300);
  });
  observer.observe(document.body, { childList: true, subtree: true });

  // console.log('Tutor video tracker initialized:', { postId, requiredPct, configuredDuration });
});

