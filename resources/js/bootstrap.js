// =============================================================================
// JavaScript do Bootstrap 5.
//
// O `bootstrap.bundle` já inclui o Popper, exigido por dropdown, tooltip e
// popover. Bootstrap 5 NÃO usa jQuery — não instale jQuery por causa dele.
// =============================================================================

import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.js';

// Exposto no window para permitir uso pontual em Blade (ex.: abrir um modal
// programaticamente) sem precisar de mais um entrypoint de bundle.
window.bootstrap = bootstrap;

export default bootstrap;
