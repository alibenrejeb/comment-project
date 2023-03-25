import React from "react";
import { useState } from "react";
import {
  GoogleReCaptchaProvider,
  GoogleReCaptcha,
} from "react-google-recaptcha-v3";
import env from "react-dotenv";
import { BsFillReplyFill } from "react-icons/bs";
import Reply from "./reply";
import { replyComment } from "../api";

function Comment(props) {
  const {
    comment,
    currentuser,
    setComments,
    setRemoveNotify,
    showBtnReply,
    showReply,
  } = props;
  const comments = props.comments;
  const [isVerified, setIsVerified] = useState(false);
  const [replyContent, setReplyContent] = useState("");

  const handleVerify = () => {
    setIsVerified(true);
  };

  const addReply = (event, commentId, replyingTo) => {
    event.preventDefault();
    if (!isVerified) {
      setRemoveNotify(["block", "oops!", "You can not comment.", "red"]);
      setTimeout(() => {
        setRemoveNotify(["none"]);
      }, 3000);
      return;
    }
    if (replyContent.length > 0) {
      setComments(
        comments.map((comment) => {
          if (comment.id == commentId) {
            let newReply = {
              content: replyContent,
              createdAt: new Date().toUTCString(),
              author: currentuser,
              replyingTo: replyingTo,
              showReplyReply: false,
            };
            async function postReply(commentId, newReply) {
              await replyComment(commentId, newReply);
            }
            postReply(commentId, newReply);
            comment.commentReplies.push(newReply);
          }
          comment.commentReplies.forEach((reply) => {
            reply["showReplyReply"] = false;
          });
          comment["showCommentReply"] = false;
          return comment;
        })
      );
      setReplyContent("");
      setRemoveNotify([
        "block",
        "Well done!",
        "You have successfully posted your reply.",
        "lightgreen",
      ]);
      setTimeout(() => {
        setRemoveNotify(["none"]);
      }, 3000);
    }
  };

  const showReplyform = (commentId) => {
    setComments(
      comments.map((comment) => {
        let newdata = { ...comment, showCommentReply: false };
        newdata.commentReplies.forEach((reply) => {
          reply["showReplyReply"] = false;
        });
        if (newdata.id === commentId) {
          newdata["showCommentReply"] = true;
          return newdata;
        }
        return newdata;
      })
    );
  };

  return (
    <div className="card" key={comment.id}>
      <div className="comment" key={comment.id}>
        <div className="userinfo">
          <img
            className="profileimg"
            src={comment.author["picture"]}
            alt=""
            referrerPolicy="no-referrer"
          />
          <span className="username">{comment.author["fullName"]}</span>
          {currentuser &&
            comment.author["fullName"] === currentuser.fullName && (
              <div className="you">you</div>
            )}
          <span className="createddate">{comment.createdAt}</span>
          {currentuser &&
            showBtnReply &&
            comment.author["fullName"] !== currentuser.fullName && (
              <button
                className="replybtn"
                onClick={() => {
                  showReplyform(comment.id);
                }}
              >
                <BsFillReplyFill />
                Reply
              </button>
            )}
        </div>
        <div className="text1">
          <p className="content">{comment.content}</p>
        </div>
      </div>
      {comment["showCommentReply"] == true && (
        <form
          onSubmit={(event) =>
            addReply(event, comment.id, comment.author["fullName"])
          }
          className="form"
        >
          <img
            className="profileimg"
            src={currentuser.picture}
            alt=""
            referrerPolicy="no-referrer"
          />
          <textarea
            className="myinput"
            name=""
            id=""
            cols="30"
            value={replyContent}
            onChange={(e) => {
              setReplyContent(e.target.value);
            }}
            rows="10"
            placeholder={"@" + comment.author["fullName"]}
          />
          <GoogleReCaptchaProvider reCaptchaKey={env.RECAPTCHA_KEY_SITE}>
            <GoogleReCaptcha onVerify={handleVerify} />
          </GoogleReCaptchaProvider>
          <button className="sendbtn" type="submit">
            Reply
          </button>
        </form>
      )}

      <div className="replies">
        {showReply &&
          comment.commentReplies.map((reply) => {
            return (
              <Reply
                key={reply.id || new Date().getTime()}
                setRemoveNotify={setRemoveNotify}
                reply={reply}
                comments={comments}
                setComments={setComments}
                comment={comment}
                currentuser={currentuser}
              />
            );
          })}
      </div>
    </div>
  );
}

export default Comment;
