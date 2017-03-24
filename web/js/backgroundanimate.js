function animationFond() {
            $('#fond').animate({"background-position-y": "0%"}, 8000, "linear").animate({"background-position-y": "100%"}, 0, animationFond);
        }
        function animationNuages() {
            $('#nuages').animate({"background-position-y": "0%"}, 5000, "linear").animate({"background-position-y": "100%"}, 0, animationNuages);
        }
        animationFond();
        animationNuages();