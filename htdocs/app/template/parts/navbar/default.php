
<nav class="nav">

    <div class="top-nav">
        <div class="group">
            <a href="/" class="entry entry-large">
                <img src="/assets/img/ghost/main.svg" alt="" class="img-responsive">
            </a>
        </div>

        <div class="group">
            <a href="/chat/" class="entry entry-link <?= $this->request->getResource() === 'chat' ? 'active' : '' ?>">
                <img src="/assets/img/icons/chat.svg" alt="" class="img-responsive">
            </a>
            <a href="/trending/" class="entry entry-link <?= $this->request->getResource() === 'trending' ? 'active' : '' ?>">
                <img src="/assets/img/icons/trending.svg" alt="" class="img-responsive entry-fix-height">
            </a>
            <a href="/stats/" class="entry entry-link <?= $this->request->getResource() === 'stats' ? 'active' : '' ?>">
                <img src="/assets/img/icons/stats.svg" alt="" class="img-responsive">
            </a>
            <a href="/history/" class="entry entry-link <?= $this->request->getResource() === 'history' ? 'active' : '' ?>">
                <img src="/assets/img/icons/history.svg" alt="" class="img-responsive">
            </a>
        </div>
    </div>

    <div class="bottom-nav">
        <div class="group">
            <?php if ($this->auth->isLogged()): ?>
                <div class="entry entry-large">
                    <img src="<?= $this->auth->getUser()->getPicture() ?>" alt="" class="img-responsive img-round">
                </div>
                <a href="<?= $this->auth->getLogoutUrl() ?>" class="entry entry-link">
                    <img src="/assets/img/icons/logout.svg" alt="" class="img-responsive">
                </a>
            <?php else: ?>
                <div class="entry entry-large">
                    <img src="/assets/img/user/connect-facebook.png" alt="" class="img-responsive img-round">
                </div>
                <a href="<?= $this->auth->getLoginUrl() ?>" class="entry entry-link">
                    <img src="/assets/img/icons/login.svg" alt="" class="img-responsive">
                </a>
            <?php endif; ?>
        </div>        
    </div>

</nav>
