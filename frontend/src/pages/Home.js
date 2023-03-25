import { useContext, useEffect, useState } from "react";
import Comment from "../components/comment";
import { getAllComments } from "../api";
import { AuthContext } from "../contexts/AuthContext";
import jwt_decode from "jwt-decode";
import InMemory from "../components/Auth/InMemory";

function Home() {
  const { setUser, setToken } = useContext(AuthContext);
  const [comments, setComments] = useState([]);
  const queryParams = new URLSearchParams(window.location.search);

  useEffect(() => {
    if (queryParams.has("token")) {
      const token = queryParams.get("token");
      const user = jwt_decode(token);
      InMemory.setUser(user);
      InMemory.setToken(token);

      setUser(user);
      setToken(token);
    }
    async function load() {
      const data = await getAllComments(0, 20);
      setComments(data);
    }
    load();
  }, []);

  return (
    <div className="container">
      {comments &&
        comments.map((comment) => {
          return (
            <Comment
              comment={comment}
              key={comment.id}
              setComments={setComments}
              comments={comments}
              showBtnReply={false}
              showReply={true}
            />
          );
        })}
    </div>
  );
}

export default Home;
