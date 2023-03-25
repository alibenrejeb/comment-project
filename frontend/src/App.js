import React from "react";
import { Routes, Route } from "react-router-dom";
import Header from "./components/Header";
import Article from "./pages/Article";
import Home from "./pages/Home";

function App() {
  return (
    <>
      <Header />
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/article/:pageId" element={<Article />} />
      </Routes>
    </>
  );
}

export default App;
