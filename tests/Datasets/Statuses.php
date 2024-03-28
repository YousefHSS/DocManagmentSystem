<?php

use App\Models\Document;

dataset('statuses', [
    Document::REJECTED,
    Document::APPROVED,
    Document::UNDER_REVISION,
    Document::UNDER_FINALIZATION,
]);
