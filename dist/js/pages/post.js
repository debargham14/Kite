$(document).ready(function() {
    $(".unliked-btn").click(function () {
        $unlikedBtn = $(this);
        let postId = $unlikedBtn.data("id");
        $likeCount = $unlikedBtn.parent().find("#likeCount");
        $.ajax({
            url: "includes/like-manager.php",
            type: 'post',
            data: {
                'unliked': 1,
                'postId': postId
            },
            dataType: 'json',
        }).done(function (response) {
            if (response.success) {
                $unlikedBtn.addClass("d-none");
                $unlikedBtn.siblings().removeClass("d-none");
                let cnt = parseInt ($likeCount.text());
                $likeCount.text(cnt+ 1);
            }
            else
                toastr.error(response.error);
        }).fail(function (xhr) {
            toastr.error(xhr.responseText);
        });
    });

    $(".liked-btn").click(function () {
        $likedBtn = $(this);
        let postId = $likedBtn.data("id");
        $likeCount = $likedBtn.parent().find("#likeCount");
        $.ajax({
            url: "includes/like-manager.php",
            type: 'post',
            data: {
                'liked': 1,
                'postId': postId
            },
            dataType: 'json',
        }).done(function (response) {
            if (response.success) {
                $likedBtn.addClass("d-none");
                $likedBtn.siblings().removeClass("d-none");
                let cnt = parseInt($likeCount.text());
                $likeCount.text(cnt - 1);
            }
            else
                toastr.error(response.error);
        }).fail(function (xhr) {
            toastr.error(xhr.responseText);
        });
    });

    $(".add-comment-form").submit(function(e) {
        e.preventDefault();
        $commentInput = $(this).find(".add-comment-text");
        const userImage = $("#user-avatar").attr("src");
        const commentText = $commentInput.val().trim();
        const postId = $(this).find(".post-id").val();
        const userName = $(this).find(".user").html();
        $commentCount = $("#commentCount");
        $commentList = $(".card-comments");
        if (commentText !== '') {
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: {
                    'add_comment': 1,
                    'postId': postId,
                    'commentText': commentText
                },
                dataType: 'json',
            }).done(function (response) {
                if (response.success) {
                    let cnt = parseInt ($commentCount.text());
                    $commentCount.text(cnt + 1);
                    $newComment = $(`<div class="card-comment" style="display: none;">
                                        <!-- User image -->
                                        <img class="img-circle img-sm" src="${userImage}" alt="User Image">
                                        <!-- Comment-Text -->
                                        <div class="comment-text">
                                            <span class="username">${userName}
                                                <span class="text-muted float-right">
                                                    Just now
                                                </span>
                                            </span>
                                            ${commentText}
                                        </div>
                                        <!-- /.comment-text -->
                                    </div>`);
                    $newComment.prependTo($commentList).show('slow');
                    $commentInput.val("");
                }
                else
                    toastr.error(response.error);
            }).fail(function(xhr) {
                toastr.error(xhr.responseText);
            });
        }
    });
});