  function writeItemHTML(post){
    var itemHTML = '';
    var cleanURL = encodeURI(post.url);
    itemHTML += '<div class="panel radius">';
    itemHTML += '<p class="itemTitle"> Recommended by '+post.posterName+'</p>';
    
    if (post.comment!=null) {
      itemHTML += '<p class="posterComment"> '+post.comment+' </p>'
    }
    
    //oEmbed/embedly api direct call
    var itemIdTag = "itemId_"+post.postId;
    itemHTML+='<div id="'+itemIdTag+'"> </div>';
    callEmbedlyAPIForDiv(itemIdTag,post.url);


    //Comments & Social
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
    itemHTML += '<div class="panel radius">';
    
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
    itemHTML += '<ul class="button-group radius even-2">';

    itemHTML += '<li><a id="like'+post.postId+'" class="button socialButton" onclick="submitLike(&#39;likes&#39;,&#39;'+post.postId+'&#39;);">'+post.likes+' Likes</a></li>';
    itemHTML += '<li><a id="love'+post.postId+'" class="button success socialButton" onclick="submitLike(&#39;loves&#39;,&#39;'+post.postId+'&#39;);">'+post.loves+' Loves</a></li>';

    itemHTML += '</ul>';
    itemHTML += '<p class="discussionStats">'+post.commentData.length+' Comments </p>';
    itemHTML += '<p class="seeDiscussion"><a data-reveal-id="detailModal" onclick="fillModal(&#39;'+post.postId+'&#39;,&#39;'+cleanURL+'&#39;,&#39;'+post.posterName+'&#39;);"> <i class="fi-comments"></i> See Discussion</a></p> </div>';

    return itemHTML;
  }

  function submitLike(likeType, postId){
    if (likeType=='ehs'||likeType=='likes'||likeType=='loves') {
      
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
   
    customEmbedHTML +=' <a href="'+obj.original_url+'" target="_blank"> See more at '+obj.provider_name+' > </a></div>';
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
  formData+='&action=postComment';
  console.log(formData);

  
  $.post(url,formData,function(response){
    console.log('Response:' + response);
    getCommentsForPost(postId);
  });

}
                
function getCommentsForPost(postId){
  var commentsHTML = '';
  $.getJSON('inc/social.php', {action:"getComments", postId:postId}, function(response){
      console.log(response);

      
      $.each(response, function(index, comment){
        commentsHTML +='<p class="commenterName"> '+comment.userName+': </p><p class="comment">'+comment.comment+'</p><p class="timeStamp"></p>';
      });

      console.log(commentsHTML);
      $('#commentSection').html(commentsHTML);
      $('#commentBox').val('');
      
  });

}