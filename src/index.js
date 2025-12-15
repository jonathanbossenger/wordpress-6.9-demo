(function (wp) {

    console.log('Demo plugin code loaded');

    const registerBlockBindingsSource = wp.blocks.registerBlockBindingsSource
    const apiFetch = wp.apiFetch;

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
        name: 'demo/go-to-book-template',
        label: 'Edit Book Template',
        callback: ({close}) => {
            document.location.href = 'site-editor.php?p=%2Fwp_template%2Ftwentytwentyfive%2F%2Fsingle-book&canvas=edit';
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

    registerBlockBindingsSource({
        name: 'demo/locations',
        label: 'Locations',
        useContext: [ 'postId', 'postType' ],
        getValues: ( { bindings } ) => {
            // this getValues assumes you're on a paragraph
            if ( bindings.address?.args?.value === 'hq' ) {
                return {
                    address:
                        '125 Main Road, Cape Town.',
                };
            }
            if ( bindings.address?.args?.value === 'south' ) {
                return {
                    address:
                        '45 2nd Ave, Kenilworth.',
                };
            }
            if ( bindings.address?.args?.value === 'north' ) {
                return {
                    address:
                        "87 Queen Street, Durbanville.",
                };
            }
            return {
                address: bindings.address,
            };
        },

        getFieldsList() {
            return [
                {
                    label: 'Headquarters',
                    type: 'string',
                    args: {
                        value: 'hq',
                    },
                },
                {
                    label: 'South Branch',
                    type: 'string',
                    args: {
                        value: 'south',
                    },
                },
                {
                    label: 'North Branch',
                    type: 'string',
                    args: {
                        value: 'north',
                    },
                },
            ];
        },
    } );

})(wp);
