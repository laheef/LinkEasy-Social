<section data-motion="create" class="motion-section section feature-section">
    <div class="container feature-grid">
        <div class="feature-copy reveal">
            <span class="eyebrow"><svg class="icon section-cue" aria-hidden="true"><use href="#i-image"/></svg>Media library</span>
            <h2>Your content library, organized</h2>
            <p>Store images, videos, reels, documents and brand assets in one shared library,
               organized into folders. Grab what you need while composing — no hunting through drives and downloads.</p>
            <ul class="check-list">
                <li>Images, video, reels and documents in one place</li>
                <li>Brand assets and folders per project</li>
                <li>Reuse media across posts and platforms</li>
                <li>Storage limits scale with your plan</li>
            </ul>
        </div>

        <div class="feature-visual reveal" data-reveal-delay="120">
            <div class="mock-frame media-mock">
                <div class="mock-frame-head">
                    <span class="mock-frame-title"><svg class="icon"><use href="#i-folder"/></svg> Media library</span>
                    <button type="button" class="mock-btn mock-btn--small"><svg class="icon"><use href="#i-upload"/></svg> Upload</button>
                </div>
                <div class="media-folders">
                    <span class="media-folder is-active"><svg class="icon"><use href="#i-folder"/></svg> All media</span>
                    <span class="media-folder"><svg class="icon"><use href="#i-image"/></svg> Images</span>
                    <span class="media-folder"><svg class="icon"><use href="#i-video"/></svg> Videos &amp; reels</span>
                    <span class="media-folder"><svg class="icon"><use href="#i-palette"/></svg> Brand assets</span>
                </div>
                <div class="media-grid">
                    <span class="media-thumb is-video">
                        <img src="<?= e(asset('assets/img/media/media-1.jpg')) ?>" alt="Hot air balloon over palm trees — video clip" loading="lazy" decoding="async" width="480" height="480">
                        <i><svg class="icon"><use href="#i-play"/></svg></i>
                        <em class="media-duration">0:14</em>
                    </span>
                    <span class="media-thumb">
                        <img src="<?= e(asset('assets/img/media/media-2.jpg')) ?>" alt="Historic domed building facade" loading="lazy" decoding="async" width="480" height="480">
                    </span>
                    <span class="media-thumb">
                        <img src="<?= e(asset('assets/img/media/media-3.jpg')) ?>" alt="Snow-covered mountain peaks" loading="lazy" decoding="async" width="480" height="480">
                    </span>
                    <span class="media-thumb is-video">
                        <img src="<?= e(asset('assets/img/media/media-4.jpg')) ?>" alt="Cable cars over turquoise water — reel" loading="lazy" decoding="async" width="480" height="480">
                        <i><svg class="icon"><use href="#i-play"/></svg></i>
                        <em class="media-duration">0:32</em>
                    </span>
                    <span class="media-thumb">
                        <img src="<?= e(asset('assets/img/media/media-5.jpg')) ?>" alt="Winding road through green hills" loading="lazy" decoding="async" width="480" height="480">
                    </span>
                    <span class="media-thumb is-video">
                        <img src="<?= e(asset('assets/img/media/media-6.jpg')) ?>" alt="Star trails over rocky ridge — reel" loading="lazy" decoding="async" width="480" height="480">
                        <i><svg class="icon"><use href="#i-play"/></svg></i>
                        <em class="media-duration">0:09</em>
                    </span>
                    <span class="media-thumb">
                        <img src="<?= e(asset('assets/img/media/media-7.jpg')) ?>" alt="Misty beach at low tide" loading="lazy" decoding="async" width="480" height="480">
                    </span>
                    <span class="media-thumb media-thumb--add"><svg class="icon"><use href="#i-plus"/></svg></span>
                </div>
            </div>
        </div>
    </div>
</section>
