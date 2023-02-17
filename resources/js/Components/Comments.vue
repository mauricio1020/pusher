<template>
     <div class="comments">
         <div class="row">
             <div class="col-md-5">
                 <div class="panel panel-primary">
                     <div class="panel-heading">
                         <span class="glyphicon glyphicon-comment"></span>Chat
                     </div>
                     <div class="panel-body">
                         <ul class="chat" id="chatWindows" style="height: 200px; overflow-y: scroll">
                             <li v-for="comment in comments" class="left clearfix" style="list-style: none">
                                 <div class="chat-body clearfix">
                                     <div class="header">
                                         <strong>
                                             {{ comment.user }}
                                         </strong>: {{ comment.comment }}
                                     </div>
                                 </div>
                             </li>
                         </ul>
                     </div>
                     <form method="post" v-on:submit.prevent="submit()" action="add_comment_url">
                         <div class="panel-footer">
                             <div class="input-group">
                                 <input
                                     v-model="comment"
                                     name="comment"
                                     class="form-control input-sm"
                                     placeholder="Escribe tu mensaje"
                                     type="text">
                                 <span class="input-group-btn">
                                     <button type="submit" class="btn btn-warning btn-sm">Enviar</button>
                                 </span>
                             </div>
                         </div>
                     </form>
                 </div>
             </div>
         </div>
     </div>
</template>

<script>

import axios from 'axios';

export default {
    props: ['add_comment_url', 'get_comments_url', 'post_id'],

    methods:{
        submit() {
            axios.post(this.add_comment_url, { comment: this.comment }).then(response => {
                this.comment = '';
            }).catch(error => {
                console.error(error);
            });
        },
        data() {
            return {
                comments: [],
                comment: ''
            }
        },
        updated() {
            var el = document.getElementById('chatWindows');
            el.scrollTop = el.scrollHeight;
        },
        mounted() {
            axios.get(this.add_comment_url).then(response => {
                _.forEach(response.data, comment => {
                    this.comments.push({
                        comment: comment.comment,
                        user: comment.name
                    })
                })
            }).catch(error => {
                console.error(error);
            });

            window.Echo.private(`comments.${this.post_id}`).listen('FireComment', e => {
                this.comments.push({
                    comment: e.comment.comment,
                    user: e.user.name
                });
            })
        }
    }
}

</script>

<style scoped>

</style>
