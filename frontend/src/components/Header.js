import React from "react";
import { NavLink } from "react-router-dom";

export default function Header() {
  return (
    <nav>
      <div className="left-nav">
        <ul>
          <li>
            <NavLink to="/">Home</NavLink>
          </li>
          <li>
            <NavLink to="/article/1">Article 1</NavLink>
          </li>
          <li>
            <NavLink to="/article/2">Article 2</NavLink>
          </li>
        </ul>
      </div>
    </nav>
  );
}
