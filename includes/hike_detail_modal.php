<?php
if (!defined('SITE_NAME')) {
    require_once dirname(__DIR__) . '/config.php';
}
$user_logged_in = isLoggedIn();
$user_can_book = isLoggedIn() && !isAdmin();
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
    var userCanBook = <?php echo $user_can_book ? 'true' : 'false'; ?>;

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
        var avg = Number(h.avg_rating || 0);
        var count = Number(h.rating_count || 0);
        var rounded = avg ? Math.round(avg * 2) / 2 : 0;
        var userRating = Number(h.user_rating || 0);

        function renderStars() {
            var html = '';
            for (var i = 1; i <= 5; i++) {
                var cls = 'rating-star';
                if (rounded >= i) {
                    cls += ' filled';
                } else if (rounded >= i - 0.5) {
                    cls += ' half';
                }
                html += '<span class="' + cls + '">★</span>';
            }
            return html;
        }

        var ratingHtml = '<div class="rating-summary">' +
            '<div class="rating-stars" aria-hidden="true">' + renderStars() + '</div>' +
            '<div class="rating-meta">';
        if (count > 0) {
            ratingHtml += '<span class="rating-score">' + avg.toFixed(1) + '</span>' +
                          '<span class="rating-count">(' + count + ')</span>';
        } else {
            ratingHtml += '<span class="rating-empty">Not rated yet</span>';
        }
        ratingHtml += '</div></div>';

        var bookHtml;
        if (!userLoggedIn) {
            bookHtml = '<p class="hike-detail-modal-auth-p">Please log in to book this hike</p>' +
                '<a href="#" class="btn btn-primary auth-modal-trigger" data-modal="login">Log in to Book</a> ' +
                '<a href="#" class="btn btn-secondary auth-modal-trigger" data-modal="register">Create Account</a>';
        } else if (!userCanBook) {
            bookHtml = '<p class="hike-detail-modal-auth-p">Admin accounts cannot book hikes. Use a regular user account to place a booking.</p>' +
                '<a href="' + encodeURI('<?php echo base_url('admin/dashboard.php'); ?>') + '" class="btn btn-secondary">Go to Admin Dashboard</a>';
        } else {
            bookHtml = '<a href="book.php?hike_id=' + h.id + '" class="btn btn-primary hike-detail-book-btn">Book This Hike</a>';
        }

        var ratingFormHtml = '';
        if (userLoggedIn) {
            ratingFormHtml =
                '<div class="rating-section" data-hike-id="' + h.id + '">' +
                '<h3 style="color: var(--primary); margin-bottom: 0.5rem;">Rate this hike</h3>' +
                '<div class="rating-form">' +
                '<div class="rating-stars-input modal-rating-input">';
            for (var i = 5; i >= 1; i--) {
                var checked = userRating === i ? ' checked' : '';
                ratingFormHtml +=
                    '<input type="radio" id="modal-rating-' + i + '" name="modal_rating" value="' + i + '"' + checked + '>' +
                    '<label for="modal-rating-' + i + '" data-value="' + i + '">★</label>';
            }
            ratingFormHtml +=
                '</div>' +
                '<button type="button" class="btn btn-primary rating-submit-btn modal-rating-submit">'
                + (userRating ? 'Update rating' : 'Submit rating') +
                '</button>' +
                '</div>' +
                '<p class="rating-note modal-rating-note">You can update your rating at any time, or open full details for more info.</p>' +
                '<div class="alert alert-success modal-rating-success" style="display:none; margin-top:0.5rem;"></div>' +
                '<div class="alert alert-error modal-rating-error" style="display:none; margin-top:0.5rem;"></div>' +
                '<div class="section-cta" style="margin-top:1rem; text-align:center;">' +
                '<a href="hike_details.php?id=' + h.id + '" class="btn btn-secondary">Open full hike details</a>' +
                '</div>' +
                '</div>';
        }

        return '<img src="' + h.image_url_esc + '" alt="' + h.name_esc + '" class="hike-detail-modal-image">' +
            '<div class="hike-detail-modal-body">' +
            '<h2 id="hike-detail-modal-title" class="hike-detail-modal-title">' + h.name_esc + '</h2>' +
            ratingHtml +
            '<div class="hike-detail-modal-location">📍 ' + h.location_esc + '</div>' +
            '<div class="hike-detail-modal-grid">' +
            '<div class="hike-detail-modal-item"><strong>Duration</strong> ⏱️ ' + h.duration_hours_min + '–' + h.duration_hours_max + ' hours</div>' +
            '<div class="hike-detail-modal-item"><strong>Price per person</strong> <span class="price">₱' + Number(h.price).toLocaleString() + '</span></div>' +
            '</div>' +
            '<div class="hike-detail-modal-about"><h3>About This Hike</h3><p>' + h.description_esc + '</p></div>' +
            ratingFormHtml +
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

                // Attach rating handlers if user is logged in
                if (userLoggedIn) {
                    var ratingSection = contentEl.querySelector('.rating-section');
                    if (ratingSection) {
                        var hikeId = ratingSection.getAttribute('data-hike-id');
                        var submitBtn = ratingSection.querySelector('.modal-rating-submit');
                        var ratingInputs = ratingSection.querySelectorAll('.modal-rating-input label');
                        var successEl = ratingSection.querySelector('.modal-rating-success');
                        var errorEl = ratingSection.querySelector('.modal-rating-error');
                        var summaryStars = contentEl.querySelector('.rating-summary .rating-stars');
                        var summaryMeta = contentEl.querySelector('.rating-summary .rating-meta');

                        function setSelected(value) {
                            var radios = ratingSection.querySelectorAll('input[name="modal_rating"]');
                            radios.forEach(function(r) {
                                r.checked = Number(r.value) === Number(value);
                            });
                        }

                        ratingInputs.forEach(function(label) {
                            label.addEventListener('click', function() {
                                var value = label.getAttribute('data-value');
                                setSelected(value);
                            });
                        });

                        function updateSummary(avg, count) {
                            if (!summaryStars || !summaryMeta) return;
                            var rounded = avg ? Math.round(avg * 2) / 2 : 0;
                            var html = '';
                            for (var i = 1; i <= 5; i++) {
                                var cls = 'rating-star';
                                if (rounded >= i) {
                                    cls += ' filled';
                                } else if (rounded >= i - 0.5) {
                                    cls += ' half';
                                }
                                html += '<span class="' + cls + '">★</span>';
                            }
                            summaryStars.innerHTML = html;
                            if (count > 0) {
                                summaryMeta.innerHTML =
                                    '<span class="rating-score">' + avg.toFixed(1) + '</span>' +
                                    '<span class="rating-count">(' + count + ')</span>';
                            } else {
                                summaryMeta.innerHTML = '<span class="rating-empty">Not rated yet</span>';
                            }
                        }

                        submitBtn.addEventListener('click', function() {
                            if (successEl) { successEl.style.display = 'none'; }
                            if (errorEl) { errorEl.style.display = 'none'; }
                            var selected = ratingSection.querySelector('input[name="modal_rating"]:checked');
                            if (!selected) {
                                if (errorEl) {
                                    errorEl.textContent = 'Please select a rating first.';
                                    errorEl.style.display = 'block';
                                }
                                return;
                            }
                            var rating = selected.value;
                            var formData = new FormData();
                            formData.append('hike_id', hikeId);
                            formData.append('rating', rating);

                            fetch('rate_hike.php', {
                                method: 'POST',
                                body: formData,
                                credentials: 'same-origin'
                            })
                            .then(function(r) { return r.json(); })
                            .then(function(res) {
                                if (!res.success) {
                                    if (errorEl) {
                                        errorEl.textContent = res.error || 'Unable to save rating.';
                                        errorEl.style.display = 'block';
                                    }
                                    return;
                                }
                                if (successEl) {
                                    successEl.textContent = res.message || 'Thanks for rating this hike!';
                                    successEl.style.display = 'block';
                                }
                                updateSummary(Number(res.avg_rating || 0), Number(res.rating_count || 0));
                            })
                            .catch(function() {
                                if (errorEl) {
                                    errorEl.textContent = 'Unable to save rating right now.';
                                    errorEl.style.display = 'block';
                                }
                            });
                        });
                    }
                }
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
