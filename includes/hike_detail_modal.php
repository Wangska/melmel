<?php
if (!defined('SITE_NAME')) {
    require_once dirname(__DIR__) . '/config.php';
}
$user_logged_in = isLoggedIn();
?>
<div id="hike-detail-modal-overlay" class="hike-detail-modal-overlay" aria-hidden="true">
    <div class="hike-detail-modal" role="dialog" aria-labelledby="hike-detail-modal-title" aria-modal="true">
        <button type="button" class="hike-detail-modal-close" aria-label="Close">&times;</button>
        <div id="hike-detail-modal-content" class="hike-detail-modal-content">
            <div class="hike-detail-modal-loading">Loading...</div>
        </div>
    </div>
</div>
<script>
(function() {
    var overlay = document.getElementById('hike-detail-modal-overlay');
    var contentEl = document.getElementById('hike-detail-modal-content');
    var userLoggedIn = <?php echo $user_logged_in ? 'true' : 'false'; ?>;

    function closeModal() {
        if (!overlay) return;
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
    }

    function openModal() {
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
    }

    function renderHike(h) {
        var bookHtml = userLoggedIn
            ? '<a href="book.php?hike_id=' + h.id + '" class="btn btn-primary hike-detail-book-btn">Book This Hike</a>'
            : '<p class="hike-detail-modal-auth-p">Please log in to book this hike</p>' +
              '<a href="#" class="btn btn-primary auth-modal-trigger" data-modal="login">Log in to Book</a> ' +
              '<a href="#" class="btn btn-secondary auth-modal-trigger" data-modal="register">Create Account</a>';
        return '<img src="' + h.image_url_esc + '" alt="' + h.name_esc + '" class="hike-detail-modal-image">' +
            '<div class="hike-detail-modal-body">' +
            '<h2 id="hike-detail-modal-title" class="hike-detail-modal-title">' + h.name_esc + '</h2>' +
            '<div class="hike-detail-modal-location">📍 ' + h.location_esc + '</div>' +
            '<div class="hike-detail-modal-grid">' +
            '<div class="hike-detail-modal-item"><strong>Difficulty</strong>' +
            '<span class="meta-badge ' + h.difficulty_class + '">' + h.difficulty_esc + '</span></div>' +
            '<div class="hike-detail-modal-item"><strong>Duration</strong> ⏱️ ' + h.duration_hours_min + '–' + h.duration_hours_max + ' hours</div>' +
            '<div class="hike-detail-modal-item"><strong>Price per person</strong> <span class="price">₱' + Number(h.price).toLocaleString() + '</span></div>' +
            '</div>' +
            '<div class="hike-detail-modal-about"><h3>About This Hike</h3><p>' + h.description_esc + '</p></div>' +
            '<div class="hike-detail-modal-actions">' +
            '<h3>Ready to Start Your Adventure?</h3>' + bookHtml +
            '</div></div>';
    }

    document.addEventListener('click', function(e) {
        var trigger = e.target.closest('.hike-detail-trigger');
        if (!trigger) return;
        e.preventDefault();
        var id = trigger.getAttribute('data-hike-id');
        if (!id) return;
        contentEl.innerHTML = '<div class="hike-detail-modal-loading">Loading...</div>';
        openModal();
        fetch('get_hike.php?id=' + encodeURIComponent(id))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.error) {
                    contentEl.innerHTML = '<div class="hike-detail-modal-error">' + (data.error || 'Could not load hike.') + '</div>';
                    return;
                }
                contentEl.innerHTML = renderHike(data);
                var authTriggers = contentEl.querySelectorAll('.auth-modal-trigger');
                authTriggers.forEach(function(el) {
                    el.addEventListener('click', function(ev) {
                        ev.preventDefault();
                        closeModal();
                        var modalName = el.getAttribute('data-modal');
                        setTimeout(function() {
                            var authBtn = document.querySelector('.auth-modal-trigger[data-modal="' + modalName + '"]');
                            if (authBtn) authBtn.click();
                        }, 150);
                    });
                });
            })
            .catch(function() {
                contentEl.innerHTML = '<div class="hike-detail-modal-error">Could not load hike. Please try again.</div>';
            });
    });

    if (overlay) {
        overlay.querySelector('.hike-detail-modal-close').addEventListener('click', closeModal);
        overlay.addEventListener('click', function(e) { if (e.target === overlay) closeModal(); });
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay && overlay.classList.contains('is-open')) closeModal();
    });
})();
</script>
