import { useState, useEffect } from "react";
import axios from "axios";

function ArtFeed() {
  const [array, setArray] = useState([]);

  const fetchAPI = async () => {
    const response = await axios.get("http://127.0.0.1:5000/api/artworks");
    setArray(response.data.artworks);
  };

  useEffect(() => {
    fetchAPI();
  }, []);

  return (
    <>
      {array.map((artwork, index) => (
        <div key={index}>
          <p>{artwork}</p>
        </div>
      ))}
    </>
  );
}

export default ArtFeed;
