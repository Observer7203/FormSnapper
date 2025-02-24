import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import axios from "axios";

function FormView() {
  const { id } = useParams();
  const [form, setForm] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [responses, setResponses] = useState({});

  useEffect(() => {
    axios.get(`/api/forms/${id}`)
      .then((response) => {
        setForm(response.data);
        setLoading(false);
      })
      .catch((error) => {
        console.error("Ошибка загрузки формы:", error);
        setError("Ошибка загрузки формы");
        setLoading(false);
      });
  }, [id]);

  const handleChange = (questionId, value) => {
    setResponses({ ...responses, [questionId]: value });
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    try {
      await axios.post(`/api/forms/${id}/submit`, {
        answers: responses
      });
      alert("Ответы успешно отправлены!");
      window.location.href = "/forms";
    } catch (error) {
      console.error("Ошибка при отправке ответов:", error);
      alert("Не удалось отправить ответы.");
    }
  };

  if (loading) return <p>Загрузка...</p>;
  if (error) return <p className="text-danger">{error}</p>;

  return (
    <div className="container mt-4">
      <h2 className="text-center">{form.title}</h2>
      <p className="text-center text-muted">{form.description}</p>

      <form onSubmit={handleSubmit}>
        {form.questions.map((question) => (
          <div key={question.id} className="mb-4 p-3 border rounded">
            <label className="form-label">{question.text}</label>

            {question.type === "text" && (
              <input
                type="text"
                className="form-control"
                onChange={(e) => handleChange(question.id, e.target.value)}
                required
              />
            )}

            {question.type === "number" && (
              <input
                type="number"
                className="form-control"
                onChange={(e) => handleChange(question.id, e.target.value)}
                required
              />
            )}

            {question.type === "radio" && question.options && (
              question.options.map((option, index) => (
                <div key={index} className="form-check">
                  <input
                    type="radio"
                    name={`question_${question.id}`}
                    value={option}
                    className="form-check-input"
                    onChange={(e) => handleChange(question.id, e.target.value)}
                  />
                  <label className="form-check-label">{option}</label>
                </div>
              ))
            )}

            {question.type === "checkbox" && question.options && (
              question.options.map((option, index) => (
                <div key={index} className="form-check">
                  <input
                    type="checkbox"
                    className="form-check-input"
                    onChange={(e) => {
                      const selected = responses[question.id] || [];
                      if (e.target.checked) {
                        setResponses({ ...responses, [question.id]: [...selected, option] });
                      } else {
                        setResponses({
                          ...responses,
                          [question.id]: selected.filter((val) => val !== option)
                        });
                      }
                    }}
                  />
                  <label className="form-check-label">{option}</label>
                </div>
              ))
            )}

            {question.type === "file_upload" && (
              <input
                type="file"
                className="form-control"
                onChange={(e) => handleChange(question.id, e.target.files[0])}
              />
            )}

            {question.type === "rating" && (
              <div>
                {[1, 2, 3, 4, 5].map((star) => (
                  <span
                    key={star}
                    style={{
                      fontSize: "1.5rem",
                      cursor: "pointer",
                      color: responses[question.id] >= star ? "gold" : "gray"
                    }}
                    onClick={() => handleChange(question.id, star)}
                  >
                    ★
                  </span>
                ))}
              </div>
            )}

            {question.type === "scale" && (
              <input
                type="range"
                min="1"
                max={question.maxScale || 10}
                className="form-range"
                onChange={(e) => handleChange(question.id, e.target.value)}
              />
            )}
          </div>
        ))}

        <button type="submit" className="btn btn-primary mt-3">Отправить ответы</button>
      </form>
    </div>
  );
}

export default FormView;
