(function (wp) {

    const apiFetch = wp.apiFetch;

    console.log('Demo plugin loaded');

    wp.data.dispatch(wp.commands.store).registerCommand({
        name: 'demo/intro',
        label: 'Show Intro Post',
        callback: ({close}) => {
            document.location.href = 'post.php?post=1&action=edit';
            close();
        },
    });

    wp.data.dispatch(wp.commands.store).registerCommand({
        name: 'demo/credits',
        label: 'Show Credits Page',
        callback: ({close}) => {
            document.location.href = 'credits.php';
            close();
        },
    });

    wp.data.dispatch(wp.commands.store).registerCommand({
        name: 'demo/meditations',
        label: 'Meditations',
        callback: ({close}) => {
            document.location.href = 'post.php?post=8&action=edit';
            close();
        },
    });

    wp.data.dispatch(wp.commands.store).registerCommand({
        name: 'demo/go-to-index-template',
        label: 'Edit Index Template',
        callback: ({close}) => {
            document.location.href = 'site-editor.php?p=%2Fwp_template%2Ftwentytwentyfive%2F%2Findex&canvas=edit';
            close();
        },
    });

    wp.data.dispatch(wp.commands.store).registerCommand({
        name: 'demo/create-book',
        label: 'Create book',
        callback: ({close}) => {
            apiFetch( {
                path: '/wp-abilities/v1/abilities/wp69-demo/create-book/run',
                method: 'POST',
            } ).then( ( response ) => {
                alert( response );
                document.location.reload();
                close();
            } );
        },
    });

    wp.data.dispatch(wp.commands.store).registerCommand({
        name: 'demo/reset-book',
        label: 'Reset book',
        callback: ({close}) => {
            apiFetch( {
                path: '/wp-abilities/v1/abilities/wp69-demo/reset-book/run',
                method: 'POST',
            } ).then( ( response ) => {
                alert( response );
                document.location.reload();
                close();
            } );
        },
    });

})(wp);
