  function writeItemHTML(post){
    var itemHTML = '';
    var cleanURL = encodeURI(post.url);
    itemHTML += '<div class="panel">';
    itemHTML += '<p class="itemTitle"> Recommended by '+post.posterName+'</p>';
    
    if (post.comment!=null) {
      itemHTML += '<p class="posterComment"> '+post.comment+' </p>'
    }
    
    //oEmbed/embedly api direct call
    var itemIdTag = "itemId_"+post.postId;
    itemHTML+='<div id="'+itemIdTag+'"> </div>';
    callEmbedlyAPIForDiv(itemIdTag,post.url);



    //Comments & Social
    itemHTML += '<p style="font-size:.60em; margin-bottom:2px; margin-top:-32px;"><a onclick="sharePost(&#39;'+post.url+'&#39;);"><i class="fi-share" style="display:inline-block; color:#9164ab; margin-left-5px;"></i></a>';
    itemHTML += '<a onclick="emailPost(&#39;'+post.url+'&#39;);"><i class="fi-mail" style="display:inline-block; color:#9164ab; margin-left:10px;"></i></a></p>';

    itemHTML += '<ul class="button-group radius even-2">';

    if (post.postLiked) {
      itemHTML += '<li><a id="like'+post.postId+'" class="button socialButton" onclick="submitLike(&#39;likes&#39;,&#39;'+post.postId+'&#39;);">'+post.likes+' Likes</a></li>';
      itemHTML += '<li><a id="love'+post.postId+'" class="button success socialButton" onclick="submitLike(&#39;loves&#39;,&#39;'+post.postId+'&#39;);">'+post.loves+' Loves</a></li>';

      
      
    } else{
      itemHTML += '<li><a id="like'+post.postId+'" class="button socialButton" onclick="submitLike(&#39;likes&#39;,&#39;'+post.postId+'&#39;);">Like It</a></li>';
      itemHTML += '<li><a id="love'+post.postId+'" class="button success socialButton" onclick="submitLike(&#39;loves&#39;,&#39;'+post.postId+'&#39;);">Love It</a></li>';
    }

    itemHTML += '</ul>';

    


    itemHTML += '<p class="discussionStats">'+post.commentData.length+' Comments </p>';
    itemHTML += '<p class="seeDiscussion"><a data-reveal-id="detailModal" onclick="fillModal(&#39;'+post.postId+'&#39;,&#39;'+cleanURL+'&#39;,&#39;'+post.posterName+'&#39;);"> <i class="fi-comments"></i> See Discussion</a></p> </div>';

    return itemHTML;
  }

  function writeItemHTMLForLibrary(post){
    var itemHTML = '';
    var cleanURL = encodeURI(post.url);
    itemHTML += '<div class="panel">';
    
    var postedString = '';
    if(post.postCount>1){
    	for (var i = 0; i < post.groupList.length; i++) {
  	postedString += post.groupList[i]+", ";
	};
	postedString = postedString.slice(0,-2);
    }
    else{
    	postedString = post.groupList[0];
    }
    itemHTML += '<p class="itemTitle"> Posted to '+postedString+'</p>';
    
    if (post.comment!=''&& post.comment!=null) {
      itemHTML += '<p class="posterComment"> '+post.comment+' </p>'
    }
    
    //oEmbed/embedly api direct call
    var itemIdTag = "itemId_"+post.postId;
    itemHTML+='<div id="'+itemIdTag+'"> </div>';
    callEmbedlyAPIForDiv(itemIdTag,post.url);


    //Comments & Social
    itemHTML += '<p style="font-size:.60em; margin-bottom:2px; margin-top:-32px;"><a onclick="sharePost(&#39;'+post.url+'&#39;);"><i class="fi-share" style="display:inline-block; color:#9164ab; margin-left-5px;"></i></a>';
    itemHTML += '<a onclick="emailPost(&#39;'+post.url+'&#39;);"><i class="fi-mail" style="display:inline-block; color:#9164ab; margin-left:10px;"></i></a></p>';

    itemHTML += '<ul class="button-group radius even-2">';

    itemHTML += '<li><a id="like'+post.postId+'" class="button socialButton" onclick="submitLike(&#39;likes&#39;,&#39;'+post.postId+'&#39;);">'+post.likes+' Likes</a></li>';
    itemHTML += '<li><a id="love'+post.postId+'" class="button success socialButton" onclick="submitLike(&#39;loves&#39;,&#39;'+post.postId+'&#39;);">'+post.loves+' Loves</a></li>';

    itemHTML += '</ul>';
    itemHTML += '<p class="discussionStats">'+post.commentData.length+' Comments </p>';
    itemHTML += '<p class="seeDiscussion"><a data-reveal-id="detailModal" onclick="fillModal(&#39;'+post.postId+'&#39;,&#39;'+cleanURL+'&#39;,&#39;'+post.posterName+'&#39;);"> <i class="fi-comments"></i> See Discussion</a></p> </div>';

    return itemHTML;
  }

  function sharePost(postURL){
    ga("send", "event", "Share", "Start");
    $('#newPostUrl').val(postURL);
    $('#newPostModal').foundation('reveal', 'open');

  }

  function emailPost(postURL){
    console.log(postURL);
    ga("send", "event", "Email", "Start");
    $('[name=emailBody]').val('Saw this on Clique and thought of you: '+postURL);
    $('#emailModal').foundation('reveal', 'open');
    //launch the email modal

  }

  function submitLike(likeType, postId){
    if (likeType=='ehs'||likeType=='likes'||likeType=='loves') {
      
      if (likeType=='likes') {
        ga("send", "event", "Like");
      } else if (likeType=='loves') {
        ga("send", "event", "Love");
      }
       
      $.getJSON('inc/social.php',{action:"submitLike",likeType:likeType, postId:postId},function(response){
        

        if (response) {
          $('#like'+postId).html(response[0]['likes']+" Likes");
          $('#love'+postId).html(response[0]['loves']+" Loves");
        }

      });

    }
  }
   
function callEmbedlyAPIForDiv(itemIdTag, postURL){
  postURL = postURL.replace(/[\n\r]/g, '');
  $.embedly.defaults.key = '45fd51c22ca84b899138d08c845884d1';

  $.embedly.oembed(postURL).done(function(results){

    obj=results[0];
    var customEmbedHTML = '';
    customEmbedHTML +='<div class="panel customEmbedCard"><h5 class="itemHeadline"> '+obj.title+' </h5>';

    if (obj.html) {
      customEmbedHTML +='<div class="flex-video">';
      customEmbedHTML +=obj.html;
      customEmbedHTML +='</div>';

    } else if (obj.thumbnail_url) {
      customEmbedHTML +='<img src="'+obj.thumbnail_url+'">';

    }

    if (obj.description) {
       customEmbedHTML +='<p class="objectDesc">'+obj.description+'</p>';
    } else{
       customEmbedHTML +='<p class="objectDesc">No description! How mysterious...check out the link below to see more</p>';
    }
   
    customEmbedHTML +=' <a href="'+obj.original_url+'" target="_blank"> See the rest at '+obj.provider_name+' > </a></div>';
    $('#'+itemIdTag).html(customEmbedHTML);
  });
}

function fillModal(postId, postURL, posterName){
  postURL = decodeURI(postURL);
  var modalItemIdTag = "modalItemId_"+postId;
  modalHTML='<div id="'+modalItemIdTag+'"> </div>';
  callEmbedlyAPIForDiv(modalItemIdTag,postURL);
  $('#detailModalContent').html(modalHTML);

  //comments
  getCommentsForPost(postId);
  

  //comment button
  var commentButtonHTML = '<a class="button postfix" onclick="postComment(&#39;'+postId+'&#39;);"> Post </a>';
  $('#postCommentButton').html(commentButtonHTML);

}

function postComment(postId){
  var url= 'inc/social.php';
  var formData = 'postId='+postId+'&comment='+$('#commentBox').val();
  ga("send", "event", "Comment");
  formData+='&action=postComment';
 

  
  $.post(url,formData,function(response){

    getCommentsForPost(postId);
  });

}
                
function getCommentsForPost(postId){
  var commentsHTML = '';
  $.getJSON('inc/social.php', {action:"getComments", postId:postId}, function(response){
      

      
      $.each(response, function(index, comment){
        commentsHTML +='<p class="commenterName"> '+comment.userName+': </p><p class="comment">'+comment.comment+'</p><p class="timeStamp"></p>';
      });


      $('#commentSection').html(commentsHTML);
      $('#commentBox').val('');
      
  });

}