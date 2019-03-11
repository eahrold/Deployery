<template>
    <pub-key
        :pub-key='userPubKey'
        heading='Add this key to your GitHub/BitBucket repo host'>
    </pub-key>
</template>

<script type="text/javascript">

export default {
    computed : {
        userPubKey () {
            return _.get(window.Deployery, 'userPubKey', '');
        }
    }
}
</script>

<style scoped lang="scss">
.pubkey {
    word-break: break-all;
}
</style>
