import React from "react";

function Reply(props) {
  let reply = props.reply;
  let currentuser = props.currentuser;

  return (
    <div className="card" key={reply.id}>
      <div className="reply">
        <div className="userinfo">
          <img className="profileimg" src={reply.author["picture"]} alt="" />
          <span className="username">{reply.author["fullName"]}</span>
          {currentuser && reply.author["fullName"] === currentuser.fullName && (
            <div className="you">you</div>
          )}
          <span className="createddate">{reply.createdAt}</span>
        </div>

        <div className="text1">
          <p className="content">
            <span className="replyto">@{reply.replyingTo}</span> {reply.content}
          </p>
        </div>
      </div>
    </div>
  );
}

export default Reply;
