import { createContext, useState } from "react";
import InMemory from "../components/Auth/InMemory";

export const AuthContext = createContext({});

export const AuthProvider = (props) => {
    const [user, setUser] = useState(InMemory.getUser());
    const [token, setToken] = useState(InMemory.getToken());
    const [rating, setRating] = useState(0);

  return (
    <AuthContext.Provider
      value={{ user, setUser, token, setToken, rating, setRating }}
    >
      {props.children}
    </AuthContext.Provider>
  );
};
