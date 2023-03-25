import Cookies from 'js-cookie';

const inMemory = () => {
    const getToken = () => {
        return Cookies.get("access_token");
    };

    const setToken = (token) => {
        Cookies.set("access_token", token);
    };

    const getUser = () => {
        if (Cookies.get("user")) {
          return JSON.parse(Cookies.get("user"));
        }
        return null;
    };

    const setUser = (user) => {
      Cookies.set("user", JSON.stringify(user));
    };

    const removeSession = () => {
        Cookies.remove("access_token", { path: "/" });
        Cookies.remove("user", { path: "/" });
    };

    return {
        getToken,
        setToken,
        getUser,
        setUser,
        removeSession
    };
};

export default inMemory();
