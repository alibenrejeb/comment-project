import React, { useState } from "react";
import { useContext } from "react";
import { AuthContext } from "../contexts/AuthContext";

const StarRating = () => {
  const { rating, setRating } = useContext(AuthContext);
  const [hover, setHover] = useState(0);

  return (
    <div className="star-rating">
      {[...Array(5)].map((star, index) => {
        index += 1;
        return (
          <button
            type="button"
            key={index}
            className={
              index <= (hover || rating) ? "on rating-button" : "off rating-button"
            }
            onClick={() => setRating(index)}
            onMouseEnter={() => setHover(index)}
            onMouseLeave={() => setHover(rating)}
          >
            <span className="star">&#9733;</span>
          </button>
        );
      })}
    </div>
  );
};

export default StarRating;
