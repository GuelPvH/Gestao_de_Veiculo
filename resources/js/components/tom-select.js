import TomSelect from 'tom-select';

/**
 * Inicialização declarativa: qualquer `<select data-tom-select>` vira um
 * Tom Select, com as opções vindas do próprio atributo em JSON.
 *
 * Uma função reaproveitável em vez de `new TomSelect()` copiado em cada view —
 * e idempotente, para poder ser rechamada após HTML injetado dinamicamente.
 *
 * @param {ParentNode} root
 * @returns {TomSelect[]}
 */
export function initTomSelect(root = document) {
    const instances = [];

    root.querySelectorAll('[data-tom-select]').forEach((el) => {
        if (el.tomselect) {
            return;
        }

        let options = {};
        const raw = el.dataset.tomSelect;

        if (raw && raw.trim() !== '') {
            try {
                options = JSON.parse(raw);
            } catch (error) {
                // Silenciar aqui esconderia um JSON malformado na view e o
                // campo simplesmente não funcionaria, sem pista do porquê.
                console.error('[tom-select] data-tom-select nao e um JSON valido:', raw, error);
                return;
            }
        }

        instances.push(new TomSelect(el, options));
    });

    return instances;
}

export default initTomSelect;
