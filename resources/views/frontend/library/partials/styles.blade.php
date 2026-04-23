<style>
    :root {
        --ac-primary: #9a3412;
        --ac-primary-dark: #7c2d12;
        --ac-bg: #fafaf9;
        --ac-dark: #1c1917;
        --ac-olive: #606c38;
        --ac-border: rgba(154, 52, 18, .12);
        --ac-muted: #78716c;
        --ac-text: #1c1917;
        --ac-card: #ffffff;
    }

    .ac-library-page,
    .ac-library-detail {
        background: var(--ac-bg);
        color: var(--ac-text);
    }

    .ac-library-hero {
        border-bottom: 1px solid var(--ac-border);
        padding: 72px 0 64px;
        background-color: var(--ac-bg);
        background-image: radial-gradient(circle at 2px 2px, rgba(154, 52, 18, 0.05) 1px, transparent 0);
        background-size: 24px 24px;
    }

    .ac-library-hero-inner {
        max-width: 900px;
        margin: 0 auto;
        text-align: center;
    }

    .ac-library-hero h1 {
        font-family: 'Lora', serif;
        font-size: clamp(2.3rem, 5vw, 4.5rem);
        font-weight: 700;
        letter-spacing: -0.04em;
        margin-bottom: 18px;
        font-style: italic;
    }

    .ac-library-hero p {
        max-width: 760px;
        margin: 0 auto 36px;
        color: var(--ac-muted);
        font-size: 1.08rem;
        line-height: 1.7;
    }

    .ac-library-search {
        background: #fff;
        border: 1px solid var(--ac-border);
        border-radius: 16px;
        padding: 9px;
        display: flex;
        align-items: center;
        box-shadow: 0 20px 60px rgba(154, 52, 18, .08);
        margin-bottom: 24px;
    }

    .ac-search-icon {
        width: 48px;
        color: #a8a29e;
        font-size: 1.3rem;
        display: flex;
        justify-content: center;
    }

    .ac-library-search input {
        flex: 1;
        border: 0;
        outline: 0;
        font-size: 1rem;
        padding: 14px 8px;
        background: transparent;
    }

    .ac-library-search button,
    .ac-btn-primary {
        border: 0;
        border-radius: 10px;
        background: var(--ac-primary);
        color: #fff;
        font-weight: 800;
        padding: 13px 28px;
        box-shadow: 0 14px 28px rgba(154, 52, 18, .2);
        transition: .2s ease;
        text-decoration: none;
    }

    .ac-library-search button:hover,
    .ac-btn-primary:hover {
        background: var(--ac-primary-dark);
        color: #fff;
    }

    .ac-library-filters {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
    }

    .ac-library-filters select {
        border: 1px solid var(--ac-border);
        background: #fff;
        border-radius: 10px;
        padding: 11px 16px;
        color: #44403c;
        min-width: 170px;
        box-shadow: 0 10px 24px rgba(28, 25, 23, .04);
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2378716c'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 40px;
    }

    .ac-library-section {
        padding: 72px 0;
    }

    .ac-soft-section {
        background: rgba(154, 52, 18, .04);
    }

    .ac-section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 34px;
    }

    .ac-section-head h2,
    .ac-block-title {
        font-family: 'Lora', serif;
        font-size: 1.65rem;
        font-weight: 700;
        margin: 0;
    }

    .ac-section-head h2 {
        border-left: 4px solid var(--ac-primary);
        padding-left: 14px;
        font-style: italic;
    }

    .ac-section-head a {
        color: var(--ac-primary);
        font-weight: 700;
        text-decoration: none;
        font-size: .92rem;
    }

    .ac-resource-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 32px;
    }

    .ac-resource-card {
        background: #fff;
        border: 1px solid var(--ac-border);
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        transition: .25s ease;
    }

    .ac-resource-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 28px 80px rgba(154, 52, 18, .12);
    }

    .ac-card-image {
        display: block;
        height: 220px;
        background: rgba(154, 52, 18, .06);
        overflow: hidden;
        position: relative;
    }

    .ac-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: .45s ease;
    }

    .ac-resource-card:hover .ac-card-image img {
        transform: scale(1.06);
    }

    .ac-card-body {
        padding: 24px;
    }

    .ac-card-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .ac-card-meta span {
        background: rgba(154, 52, 18, .1);
        color: var(--ac-primary);
        text-transform: uppercase;
        font-size: .64rem;
        font-weight: 900;
        padding: 4px 8px;
        border-radius: 5px;
        letter-spacing: .05em;
    }

    .ac-card-meta em {
        color: #a8a29e;
        font-family: 'Lora', serif;
        font-size: .9rem;
    }

    .ac-card-body h3 {
        font-family: 'Lora', serif;
        font-size: 1.25rem;
        line-height: 1.25;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .ac-card-body h3 a {
        color: var(--ac-text);
        text-decoration: none;
    }

    .ac-card-author {
        color: #78716c;
        font-style: italic;
        font-size: .9rem;
        margin-bottom: 14px;
    }

    .ac-card-excerpt {
        color: #78716c;
        font-size: .9rem;
        line-height: 1.65;
        min-height: 72px;
    }

    .ac-card-button {
        display: block;
        margin-top: 20px;
        width: 100%;
        border: 2px solid var(--ac-primary);
        color: var(--ac-primary);
        border-radius: 9px;
        padding: 10px;
        font-size: .9rem;
        font-weight: 800;
        text-align: center;
        text-decoration: none;
        transition: .2s ease;
    }

    .ac-card-button:hover {
        background: var(--ac-primary);
        color: #fff;
    }

    .is-locked .ac-card-image img,
    .is-locked .ac-card-body h3,
    .is-locked .ac-card-excerpt {
        filter: blur(2px);
    }

    .ac-lock-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(28, 25, 23, .88);
        color: #fff;
        border-radius: 999px;
        padding: 6px 11px;
        font-size: .76rem;
        font-weight: 800;
        display: inline-flex;
        gap: 5px;
        align-items: center;
    }

    .ac-latest-list {
        display: grid;
        gap: 16px;
    }

    .ac-list-resource {
        background: #fff;
        border: 1px solid var(--ac-border);
        border-radius: 15px;
        padding: 22px;
        display: flex;
        gap: 22px;
        transition: .2s ease;
        cursor: pointer;
    }

    .ac-list-resource:hover {
        border-color: rgba(154, 52, 18, .45);
    }

    .ac-list-cover {
        width: 60px;
        height: 84px;
        border-radius: 8px;
        background: var(--ac-bg);
        border: 1px solid rgba(154, 52, 18, .07);
        overflow: hidden;
        position: relative;
        flex: 0 0 60px;
        display: block;
    }

    .ac-list-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ac-list-lock-overlay {
        position: absolute;
        inset: 0;
        background: rgba(28, 25, 23, .45);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .ac-list-body {
        flex: 1;
        min-width: 0;
    }

    .ac-list-title-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
    }

    .ac-list-title-row h3 {
        font-size: 1.05rem;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .ac-list-title-row h3 a {
        color: var(--ac-text);
        text-decoration: none;
    }

    .ac-list-title-row h3 a:hover {
        color: var(--ac-primary);
    }

    .ac-list-body p {
        color: #78716c;
        font-size: .9rem;
        font-style: italic;
        margin-bottom: 10px;
    }

    .ac-list-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
    }

    .ac-list-tags span {
        background: #f5f5f4;
        color: #78716c;
        border-radius: 5px;
        padding: 4px 8px;
        font-size: .7rem;
    }

    .ac-library-sidebar {
        display: grid;
        gap: 36px;
    }

    .ac-sidebar-block h3 {
        font-family: 'Lora', serif;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 18px;
    }

    .ac-recommend-box {
        background: #fff;
        border: 1px solid var(--ac-border);
        border-radius: 15px;
        padding: 20px;
        display: grid;
        gap: 16px;
    }

    .ac-recommend-item {
        display: block;
        text-decoration: none;
        color: var(--ac-text);
        border-bottom: 1px solid rgba(154, 52, 18, .07);
        padding-bottom: 14px;
    }

    .ac-recommend-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .ac-recommend-item span {
        display: block;
        color: var(--ac-primary);
        font-size: .68rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 5px;
    }

    .ac-recommend-item strong {
        display: block;
        line-height: 1.35;
        font-size: .92rem;
    }

    .ac-recommend-item small {
        color: #78716c;
    }

    .ac-topic-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .ac-topic-card {
        background: #fff;
        border: 1px solid var(--ac-border);
        border-radius: 11px;
        text-align: center;
        padding: 18px 10px;
        color: var(--ac-text);
        text-decoration: none;
        transition: .2s ease;
    }

    .ac-topic-card:hover {
        border-color: var(--ac-primary);
        color: var(--ac-primary);
    }

    .ac-topic-card i, .ac-topic-card .material-symbols-outlined {
        display: block;
        color: var(--ac-primary);
        font-size: 1.5rem;
        margin-bottom: 8px;
    }

    .ac-topic-card span {
        font-weight: 800;
        font-size: .82rem;
    }

    .ac-connect-section {
        padding: 80px 0;
        background: var(--ac-bg);
    }

    .ac-connect-card {
        background: var(--ac-dark);
        border-radius: 28px;
        padding: clamp(32px, 6vw, 64px);
        color: #fff;
        display: flex;
        align-items: center;
        gap: 60px;
        overflow: hidden;
        position: relative;
    }

    .ac-connect-content {
        flex: 1;
        position: relative;
        z-index: 2;
    }

    .ac-connect-content h2 {
        font-family: 'Lora', serif;
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 700;
        margin-bottom: 20px;
        font-style: italic;
    }

    .ac-connect-content p {
        color: #d6d3d1;
        font-size: 1.04rem;
        line-height: 1.7;
        max-width: 720px;
        margin-bottom: 32px;
    }

    .ac-connect-links {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .ac-connect-links a {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        color: #fff;
        text-decoration: none;
        background: rgba(255, 255, 255, .06);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 12px;
        padding: 16px;
    }

    .ac-connect-links .material-symbols-outlined {
        color: var(--ac-primary);
        background: rgba(154, 52, 18, .18);
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.25rem;
    }

    .ac-connect-links strong,
    .ac-connect-links small {
        display: block;
    }

    .ac-connect-links small {
        color: #a8a29e;
        font-size: .78rem;
    }

    .ac-connect-art {
        flex: 0 0 290px;
        display: flex;
        justify-content: center;
    }

    .ac-orbit {
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: rgba(154, 52, 18, .28);
        padding: 36px;
        border: 2px dashed rgba(154, 52, 18, .5);
    }

    .ac-orbit img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .ac-breadcrumb {
        display: flex;
        gap: 10px;
        align-items: center;
        padding: 34px 0;
        color: #78716c;
        font-size: .92rem;
    }

    .ac-breadcrumb a {
        color: #78716c;
        text-decoration: none;
    }

    .ac-breadcrumb a:hover {
        color: var(--ac-primary);
    }

    .ac-resource-hero {
        display: flex;
        gap: 36px;
        align-items: flex-start;
        margin-bottom: 64px;
    }

    .ac-detail-cover {
        width: 250px;
        height: 340px;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 28px 80px rgba(154, 52, 18, .16);
        background: rgba(154, 52, 18, .06);
        position: relative;
        flex: 0 0 250px;
    }

    .ac-detail-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ac-cover-lock {
        position: absolute;
        inset: 0;
        background: rgba(28, 25, 23, .55);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 2.5rem;
    }

    .ac-detail-heading {
        flex: 1;
        padding-top: 8px;
    }

    .ac-badge-row {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 18px;
    }

    .ac-badge {
        background: rgba(154, 52, 18, .1);
        color: var(--ac-primary);
        text-transform: uppercase;
        letter-spacing: .08em;
        border-radius: 5px;
        padding: 6px 9px;
        font-weight: 900;
        font-size: .72rem;
        text-decoration: none;
    }

    .ac-badge-olive {
        color: var(--ac-olive);
        background: rgba(96, 108, 56, .12);
    }

    .ac-badge-lock {
        color: #fff;
        background: var(--ac-dark);
    }

    .ac-detail-heading h1 {
        font-family: 'Lora', serif;
        font-size: clamp(2.4rem, 5vw, 4.3rem);
        line-height: 1.05;
        font-weight: 800;
        margin-bottom: 18px;
    }

    .ac-author-line {
        font-size: 1.25rem;
        color: #78716c;
    }

    .ac-author-line em {
        color: var(--ac-primary);
    }

    .ac-detail-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 28px;
    }

    .ac-btn {
        border-radius: 10px;
        padding: 13px 22px;
        font-weight: 900;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 0;
    }

    .ac-btn-outline {
        border: 1px solid rgba(154, 52, 18, .18);
        background: #fff;
        color: var(--ac-primary);
    }

    .ac-btn-outline:hover {
        background: rgba(154, 52, 18, .06);
        color: var(--ac-primary);
    }

    .ac-btn-icon {
        width: 50px;
        height: 50px;
        padding: 0;
        justify-content: center;
        background: #fff;
        border: 1px solid #e7e5e4;
        color: var(--ac-text);
    }

    .ac-detail-main {
        display: grid;
        gap: 48px;
    }

    .ac-detail-section h2 {
        font-family: 'Lora', serif;
        font-size: 1.6rem;
        font-weight: 800;
        border-bottom: 1px solid var(--ac-border);
        padding-bottom: 12px;
        margin-bottom: 18px;
        font-style: italic;
    }

    .ac-detail-section p {
        color: #44403c;
        line-height: 1.85;
        font-size: 1.05rem;
    }

    .ac-document-preview {
        border: 1px solid #e7e5e4;
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(28, 25, 23, .04);
    }

    .ac-preview-head {
        background: #f5f5f4;
        border-bottom: 1px solid #e7e5e4;
        padding: 15px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ac-preview-head > span {
        color: #78716c;
        text-transform: uppercase;
        font-weight: 900;
        letter-spacing: .12em;
        font-size: .78rem;
    }

    .ac-preview-head div {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ac-preview-head button {
        border: 0;
        background: #fff;
        color: #78716c;
        border-radius: 6px;
        width: 34px;
        height: 30px;
    }

    .ac-preview-frame {
        height: 720px;
        background: #e7e5e4;
    }

    .ac-preview-frame iframe {
        width: 100%;
        height: 100%;
        border: 0;
    }

    .ac-document-preview:fullscreen {
        padding: 2rem;
        background: #fff;
        width: 100vw;
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .ac-document-preview:fullscreen .ac-preview-frame {
        flex: 1;
        height: auto;
    }

    .ac-document-preview:fullscreen .ac-preview-head {
        border-radius: 0;
    }

    .ac-preview-locked,
    .ac-preview-empty {
        min-height: 620px;
        background: #f5f5f4;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 36px;
        text-align: center;
        color: #78716c;
    }

    .ac-preview-locked .material-symbols-outlined,
    .ac-preview-empty .material-symbols-outlined {
        font-size: 4rem;
        color: #a8a29e;
        margin-bottom: 16px;
    }

    .ac-preview-locked h3 {
        color: var(--ac-text);
        font-family: 'Lora', serif;
        font-size: 1.7rem;
        font-weight: 800;
    }

    .ac-chip-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .ac-chip-row a {
        border: 1px solid rgba(154, 52, 18, .18);
        background: rgba(154, 52, 18, .05);
        color: var(--ac-primary);
        border-radius: 999px;
        padding: 8px 14px;
        text-decoration: none;
        font-size: .88rem;
        font-weight: 700;
    }

    .ac-detail-sidebar {
        display: grid;
        gap: 32px;
        position: sticky;
        top: 90px;
    }

    .ac-meta-card,
    .ac-citation-card {
        background: #fff;
        border: 1px solid #e7e5e4;
        border-radius: 16px;
        padding: 24px;
    }

    .ac-meta-card h3,
    .ac-citation-card h3,
    .ac-side-block h3 {
        text-transform: uppercase;
        letter-spacing: .13em;
        color: #a8a29e;
        font-size: .78rem;
        font-weight: 900;
        margin-bottom: 22px;
    }

    .ac-meta-row {
        margin-bottom: 16px;
    }

    .ac-meta-row span {
        display: block;
        color: #a8a29e;
        text-transform: uppercase;
        letter-spacing: .06em;
        font-size: .72rem;
        font-weight: 800;
        margin-bottom: 5px;
    }

    .ac-meta-row strong {
        display: block;
        color: var(--ac-text);
        font-weight: 700;
    }

    .ac-citation-card {
        background: rgba(154, 52, 18, .06);
        border-color: var(--ac-border);
    }

    .ac-citation-card h3 {
        color: var(--ac-primary);
    }

    .ac-citation-text {
        background: rgba(255, 255, 255, .75);
        border-radius: 10px;
        padding: 16px;
        color: #44403c;
        font-style: italic;
        line-height: 1.6;
        font-size: .9rem;
    }

    .ac-copy-citation {
        margin-top: 14px;
        width: 100%;
        border-radius: 10px;
        border: 1px solid rgba(154, 52, 18, .18);
        background: #fff;
        color: var(--ac-primary);
        padding: 11px;
        font-weight: 900;
    }

    .ac-side-block {
        display: grid;
        gap: 14px;
    }

    .ac-learning-item,
    .ac-more-resource {
        display: flex;
        gap: 14px;
        align-items: center;
        text-decoration: none;
        color: var(--ac-text);
        padding: 10px;
        border-radius: 12px;
    }

    .ac-learning-item:hover,
    .ac-more-resource:hover {
        background: #fff;
    }

    .ac-learning-item .material-symbols-outlined {
        width: 58px;
        height: 58px;
        background: rgba(96, 108, 56, .12);
        color: var(--ac-olive);
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 58px;
    }

    .ac-more-resource img {
        width: 56px;
        height: 82px;
        object-fit: cover;
        border-radius: 6px;
        background: #e7e5e4;
        flex: 0 0 56px;
    }

    .ac-more-resource strong,
    .ac-more-resource small {
        display: block;
    }

    .ac-more-resource strong {
        font-size: .9rem;
        line-height: 1.3;
    }

    .ac-more-resource small {
        color: #78716c;
        margin-top: 4px;
    }

    .ac-discussion-card {
        background: #fff;
        border: 1px solid #e7e5e4;
        border-radius: 16px;
        padding: 22px;
    }

    .ac-discussion-card h3 {
        font-size: 1.05rem;
        font-weight: 800;
    }

    .ac-discussion-card a {
        color: var(--ac-primary);
        font-weight: 800;
        text-decoration: none;
    }

    .ac-empty-state {
        background: #fff;
        border: 1px dashed var(--ac-border);
        border-radius: 16px;
        padding: 36px;
        text-align: center;
    }

    .ac-empty-state h3 {
        font-family: 'Lora', serif;
        font-weight: 800;
    }

    .ac-muted {
        color: #78716c;
        margin: 0;
    }

    .ac-restriction-modal .modal-body {
        padding: 38px;
        text-align: center;
        position: relative;
    }

    .ac-modal-close {
        position: absolute;
        top: 18px;
        right: 18px;
    }

    .ac-restriction-icon {
        width: 76px;
        height: 76px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: rgba(154, 52, 18, .1);
        color: var(--ac-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }

    .ac-restriction-modal h3 {
        font-family: 'Lora', serif;
        font-weight: 800;
        font-size: 1.7rem;
    }

    .ac-restriction-modal p {
        color: #78716c;
        line-height: 1.7;
    }

    .ac-restriction-actions {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 22px;
    }

    @media (max-width: 991px) {
        .ac-resource-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ac-resource-hero {
            flex-direction: column;
        }

        .ac-detail-sidebar {
            position: static;
        }

        .ac-connect-card {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (max-width: 767px) {
        .ac-library-search {
            flex-direction: column;
            align-items: stretch;
        }

        .ac-search-icon {
            display: none;
        }

        .ac-library-search button {
            width: 100%;
        }

        .ac-resource-grid {
            grid-template-columns: 1fr;
        }

        .ac-list-resource {
            flex-direction: column;
        }

        .ac-list-tags {
            justify-content: flex-start;
        }

        .ac-connect-links {
            grid-template-columns: 1fr;
        }

        .ac-detail-cover {
            width: 100%;
            max-width: 260px;
        }

        .ac-detail-actions {
            flex-direction: column;
        }

        .ac-btn,
        .ac-btn-icon {
            width: 100%;
            justify-content: center;
        }
    }
</style>
