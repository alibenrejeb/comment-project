import axios from "axios";
import env from "react-dotenv";
import InMemory from "../components/Auth/InMemory";

const api = axios.create({ baseURL: env.API_BASE_URL });

api.interceptors.request.use((req) => {
    if (InMemory.getToken()) {
        req.headers.Authorization = `Bearer ${InMemory.getToken()}`;
    }

    return req;
});

export const getAllComments = async (pageId = 0, limit = 0) => {
  const suffix = pageId === 0 ? "" : "/" + pageId;
  const config = {
      params: {
          sort: {
              createdAt: 'DESC'
          }
    }
  };
  if (limit > 0) {
      config.params.limit = limit;
  }
  const response = await api.get("/v1/comments" + suffix, config);
  return response.data;
};

export const addComment = async (comment) => {
    const response = await api.post("/v1/comments", comment);
    return response.data;
};

export const replyComment = async (commentId, reply) => {
    const response = await api.post(`/v1/comments/${commentId}`, reply);
    return response.data;
};

export const updateComment = async (commentId, comment) => {
    const response = await api.patch(`/v1/comments/${commentId}`, comment);
    return response.data;
};
