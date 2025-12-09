(function (wp) {
    console.log('Demo plugin loaded');

    wp.data.dispatch(wp.commands.store).registerCommand({
        name: 'demo/introductions',
        label: 'Show Introductions',
        callback: ({close}) => {
            document.location.href = 'site-editor.php?p=%2Fpage&postId=22&canvas=edit';
            close();
        },
    });

    wp.data.dispatch(wp.commands.store).registerCommand({
        name: 'demo/wordpress69',
        label: 'WordPress 6.9',
        callback: ({close}) => {
            document.location.href = 'site-editor.php?p=%2Fpage&postId=17&canvas=edit';
            close();
        },
    });

    wp.data.dispatch(wp.commands.store).registerCommand({
        name: 'demo/go-to-book',
        label: 'Go to my book',
        callback: ({close}) => {
            document.location.href = 'post.php?post=14&action=edit';
            close();
        },
    });
    wp.data.dispatch(wp.commands.store).registerCommand({
        name: 'demo/go-to-index-template',
        label: 'Edit Index Template',
        callback: ({close}) => {
            document.location.href = 'post.php?post=14&action=edit';
            close();
        },
    });
})(wp);
