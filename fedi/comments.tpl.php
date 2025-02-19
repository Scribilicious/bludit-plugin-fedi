<div id="comments"></div>
<script>
    const escapeHtml = (unsafe) => {
        if (typeof unsafe !== 'string') {
            return '';     }
            return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
        };

        const replaceEmoji = (string, emojis) => {
            emojis.forEach(emoji => {
                string = string.replaceAll(`:${emoji.shortcode}:`, `<img src="${escapeHtml(emoji.static_url)}" width="20" height="20" alt="${escapeHtml(emoji.shortcode)}">`);
            });
            return string;
        };

        const loadComments = (urlApi, urlFedi, container) => {
            if (!urlApi || !urlFedi) {
                return false;
            }

            fetch(urlApi)
            .then(response => response.json())
            .then(data => {
                if (data.descendants) {
                    container.innerHTML = `
                        <h4>Comments</h4>
                        <p><button class="addComment">Add a Comment</button></p>
                        <div class="comment-list">
                        ${data.descendants.map(status => `
                                <div class="comment">
                                    <div class="avatar">
                                        <img src="${status.account.avatar_static}" height="60" width="60" alt="">
                                    </div>
                                    <div class="content">
                                        <div class="author">
                                            <a target="_blank" href="${status.account.url}" rel="nofollow">
                                                <span>${replaceEmoji(escapeHtml(status.account.display_name), status.account.emojis)}</span>
                                            </a>
                                            <a target="_blank" class="date" href="${status.url}" rel="nofollow">
                                                ${new Date(status.created_at).toLocaleString()}
                                            </a>
                                        </div>
                                        <div class="mastodon-comment-content">${replaceEmoji(status.content, status.emojis)}</div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                        ${data.descendants.length > 1 ? `<p><button class="addComment">Add a Comment</button></p>` : ''}
                        <dialog id="comment-dialog">
                            <h3>Reply to this post</h3>
                            <button title="Cancel" id="close">&times;</button>
                            <p><?= $this->getValue('reply_help_direct'); ?></p>
                            <p class="input-row">
                                <input type="text" inputmode="url" autocapitalize="none" autocomplete="off"
                                    value="${escapeHtml(localStorage.getItem('mastodonUrl')) ?? ''}" id="instanceName"
                                    placeholder="mastodon.social">
                                <button class="button" id="go">Go</button>
                            </p>
                            <p><?= $this->getValue('reply_help_copy'); ?></p>
                            <p class="input-row">
                                <input type="text" readonly id="copyInput" value="${urlFedi}">
                                <button class="button" id="copy">Copy</button>
                            </p>
                        </dialog>
                    `;

                    const dialog = document.getElementById('comment-dialog');

                    document.querySelectorAll('.addComment').forEach(button => {
                        button.addEventListener('click', () => {
                            dialog.showModal();
                            if (dialog.getBoundingClientRect().y > 100) {
                                document.getElementById('instanceName').focus();
                            }
                        });
                    });

                    document.getElementById('go').addEventListener('click', () => {
                        let url = document.getElementById('instanceName').value.trim();
                        if (!url) {
                            window.alert('Please provide the name of your instance');
                            return;
                        }

                        localStorage.setItem('mastodonUrl', url);
                        if (!url.startsWith('https://')) {
                            url = `https://${url}`;
                        }

                        window.open(`${url}/authorize_interaction?uri=${urlFedi}`, '_blank');
                    });

                    document.getElementById('instanceName').addEventListener('keydown', e => {
                        if (e.key === 'Enter') {
                            document.getElementById('go').click();
                        }
                    });

                    document.getElementById('copy').addEventListener('click', () => {
                        const copyInput = document.getElementById('copyInput');
                        copyInput.select();
                        navigator.clipboard.writeText(urlFedi);
                        const copyButton = document.getElementById('copy');
                        copyButton.textContent = 'Copied!';
                        setTimeout(() => {
                            copyButton.textContent = 'Copy';
                        }, 1000);
                    });

                    document.getElementById('close').addEventListener('click', () => {
                        dialog.close();
                    });
                    dialog.addEventListener('keydown', e => {
                        if (e.key === 'Escape') dialog.close();
                    });

                    dialog.addEventListener('click', event => {
                        const rect = dialog.getBoundingClientRect();
                        const isInDialog = rect.top <= event.clientY && event.clientY <= rect.top + rect.height &&
                        rect.left <= event.clientX && event.clientX <= rect.left + rect.width;
                        if (!isInDialog) {
                            dialog.close();
                        }
                    });
                }
            });
};

loadComments('<?= $url_api; ?>', '<?= $url_fedi; ?>', document.getElementById('comments'));
</script>