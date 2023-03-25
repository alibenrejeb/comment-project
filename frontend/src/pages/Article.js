import { useEffect, useState, useContext } from "react";
import { useParams } from "react-router-dom";
import {
  GoogleReCaptchaProvider,
  GoogleReCaptcha,
} from "react-google-recaptcha-v3";
import env from 'react-dotenv';
import Comment from "../components/comment";
import { getAllComments, addComment } from "../api";
import { AuthContext } from "../contexts/AuthContext";
import StarRating from "../components/StarRating";

function Article() {
  const [isVerified, setIsVerified] = useState(false);
  const { user, rating, setRating } = useContext(AuthContext);
  const params = useParams();
  const [comments, setComments] = useState([]);
  const [content, setContent] = useState("");
  const [removeNotiy, setRemoveNotify] = useState([
    "none",
    "Well done!",
    "You have successfully posted your comment.",
    "lightgreen",
  ]);

  const handleVerify = () => {
    setIsVerified(true);
  };

  useEffect(() => {
    async function load() {
      const data = await getAllComments(params.pageId, 10);
      console.log(data[0]);
      setComments(data);
    }
    load();
  }, [params.pageId]);

  const saveComment = (event) => {
    event.preventDefault();
    if (!isVerified) {
      setRemoveNotify(["block", "oops!", "You can not comment.", "red"]);
      setTimeout(() => {
        setRemoveNotify(["none"]);
      }, 3000);
      return;
    }

    if (content.length > 0) {
      let newComment = {};
      newComment["page_id"] = params.pageId;
      newComment["content"] = content;
      newComment["rating"] = rating;
      newComment["createdAt"] = new Date().toUTCString();
      newComment["author"] = user;
      newComment["commentReplies"] = [];
      comments.unshift(newComment);
      async function post(data) {
        await addComment(data);
      }
      post(newComment);
      setContent("");
      setRating(0);
      setRemoveNotify([
        "block",
        "Well done!",
        "You have successfully posted your comment.",
        "lightgreen",
      ]);
      setTimeout(() => {
        setRemoveNotify(["none"]);
      }, 3000);
    } else {
      setRemoveNotify([
        "block",
        "oops!!",
        "You can not post empty comment.",
        "yellow",
      ]);
      setTimeout(() => {
        setRemoveNotify(["none"]);
      }, 3000);
    }
  };

  return (
    <div className="container">
      {/* notification div */}
      <div
        className="notification"
        style={{ display: removeNotiy[0], backgroundColor: removeNotiy[3] }}
      >
        <span>{removeNotiy[1]}</span> {removeNotiy[2]}
      </div>

      {!user && (
        <div>
          <a href="http://127.0.0.1:8000/connect/google">
            S’authentifier via Google
          </a>
        </div>
      )}
      <form onSubmit={saveComment} className="form">
        {user && (
          <img
            className="profileimg"
            src={user.picture}
            alt=""
            referrerPolicy="no-referrer"
          />
        )}
        <StarRating />
        <textarea
          className="myinput"
          name=""
          id=""
          cols="30"
          value={content}
          onChange={(e) => {
            setContent(e.target.value);
          }}
          rows="10"
          placeholder="Add a comment ..."
        />
        <GoogleReCaptchaProvider reCaptchaKey={env.RECAPTCHA_KEY_SITE}>
          <GoogleReCaptcha onVerify={handleVerify} />
        </GoogleReCaptchaProvider>
        <button className="sendbtn" type="submit">
          Send
        </button>
      </form>

      {comments &&
        comments.map((comment) => {
          return (
            <Comment
              setRemoveNotify={setRemoveNotify}
              comment={comment}
              key={comment.id || new Date().getTime()}
              currentuser={user}
              setContent={setContent}
              setComments={setComments}
              comments={comments}
              content={content}
              showBtnReply={true}
              showReply={true}
            />
          );
        })}
    </div>
  );
}

export default Article;
