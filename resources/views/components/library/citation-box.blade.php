@props(['citation'])

<section class="ac-citation-card">
    <h3>Cite this Resource (APA)</h3>

    <div class="ac-citation-text" id="libraryCitationText">
        {{ $citation }}
    </div>

    <button type="button" class="ac-copy-citation" data-copy-target="libraryCitationText">
        <i class="mdi mdi-content-copy"></i>
        Copy Citation
    </button>
</section>
