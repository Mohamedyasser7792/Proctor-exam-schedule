import React from 'react';
import { Link, useLocation } from 'react-router-dom';
// import '@fortawesome/fontawesome-free/css/all.min.css';
// import 'https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap';

import './Home.css';
import p1 from '../assets/22.png';
import p2 from '../assets/jjjj.png';
import p3 from '../assets/laravel.png';
import p4 from '../assets/282599.webp';
import p8 from '../assets/PSD-files-on-Mac.jpg';
import p9 from '../assets/logo.png';
import p5 from '../assets/images.png';




const branches = [
  "Fayoum Branch",
  "Assiut Branch",
  "Ain Shams Branch",
  "Alexandria Branch",
  "Sadat Branch",
  "Monofiya Branch",
  "Sohag Branch",
  "Beni Suef Branch",
  "Qena Branch",
  "Hurghada Branch",
  "Aswan Branch",
  "Ismailia Branch",
  "Tanta Branch",
];

function BranchItem({ name, imageSrc }) {
  return (
    <div className="p2">
      <a href="https://www.eelu.edu.eg/">
        <img src={imageSrc} alt={name} />
      </a>
      <p>{name}</p>
    </div>
  );
}

const Home = () => {
  const location = useLocation();
  return (
    <div>
      {/* Header Section */}
      <header className="header">

        
        <div className="container">
          <h1 className="header-title">AlgoSched</h1>
          <h2 className="header-subtitle">Egyptian E-Learning University</h2>
          <Link to="/login">
            <button className="header-button">Login</button>
          </Link>
        </div>
      </header>


      {/* Menu Section */}
      <nav className="menu">
        <div className="container">
          <h2>
            AlgoSched <sub><small>Egyptian E-Learning University</small></sub>
          </h2>
          <ul>
            <li className={location.pathname === '/' ? 'active' : ''}>Home</li>
            <li className={location.pathname === '/web' ? 'active' : ''}>About us</li>
            <li className={location.pathname === '/desktop' ? 'active' : ''}>Packages</li>
            <li className={location.pathname === '/mobile' ? 'active' : ''}>Contact us</li>
          </ul>
        </div>
      </nav>

      {/* Courses Section */}
      <section className="courses">
        <div className="container">
          <div className="container">
            <h1 className="about-title">About AlgoSched</h1>
            <p className="about-description">
              Welcome to <strong>AlgoSched</strong> — your go-to platform for effortless university exam scheduling and management. Designed with efficiency and precision in mind, AlgoSched streamlines the often complex process of coordinating exams, teaching assistants, and study groups.
            </p>

            <h2 className="about-subtitle">Our Mission</h2>
            <p className="about-text">
              At AlgoSched, our mission is to simplify and optimize the exam scheduling process for educational institutions, ensuring seamless coordination between various university resources. We believe in empowering administrators with tools that save time, reduce conflicts, and improve organizational efficiency.
            </p>

            <h2 className="about-subtitle">What We Offer</h2>
            <ul className="about-list">
              <li><strong>Automated Exam Scheduling:</strong> Generate optimized exam schedules with a single click.</li>
              <li><strong>Comprehensive Resource Management:</strong> Efficiently manage teaching assistants, study groups, exam halls, and subjects.</li>
              <li><strong>User-Friendly Interface:</strong> An intuitive interface that makes scheduling and resource allocation straightforward.</li>
              <li><strong>Real-Time Updates:</strong> Ensure up-to-date schedules and minimize conflicts with automatic checks and updates.</li>
            </ul>

            <h2 className="about-subtitle">Why Choose AlgoSched?</h2>
            <ul className="about-list">
              <li><strong>Time-Saving Automation:</strong> Reduce manual work with intelligent scheduling algorithms.</li>
              <li><strong>Accuracy and Precision:</strong> Minimize scheduling errors and conflicts.</li>
              <li><strong>Scalability:</strong> Designed for institutions of all sizes, from small departments to large universities.</li>
              <li><strong>Accessibility:</strong> Access the platform from anywhere with ease.</li>
            </ul>

            <h2 className="about-subtitle">Who is AlgoSched for?</h2>
            <ul className="about-list">
              <li><strong>Exam Administrators:</strong> Simplify the management of exam schedules and resources.</li>
              <li><strong>Teaching Assistants:</strong> Get clear, organized schedules for invigilation duties.</li>
              <li><strong>Students:</strong> Benefit from well-coordinated and conflict-free exam timetables.</li>
            </ul>

            <p className="about-closing">
              AlgoSched is more than just a scheduling tool — it’s a step toward streamlined academic operations and a more organized university environment.
            </p>
          </div>

          <div className="image-container">
            <img className="content-image" src={p8} alt="Content" />
          </div>

          <div className="box2">
            <h2 className="courses-title">Technology used</h2>
            <div className="courses-grid">
              <div className="course-card">
                <img src={p1} alt="Course 1" />
                <p>CSS</p>
              </div>
              <div className="course-card">
                <img src={p2} alt="Course 2" />
                <p>Java Script</p>
              </div>
              <div className="course-card">
                <img src={p3} alt="Course 3" />
                <p>Laravel</p>
              </div>
              <div className="course-card">
                <img src={p4} alt="Course 4" />
                <p>Reacrt</p>
              </div>
              <div className="course-card">
                <img src={p5} alt="Course 5" />
                <p>PHP</p>
              </div>
            </div>
          </div>
        </div>
      </section>


      {/* Path Section */}
      {/* Path Section */}
      <section className="path">
        <div className="container">
          <h1 className="animated-heading">Programmatic Paths</h1>
          <div className="path-slider">
            <div className="path-track">
              {branches.concat(branches).map((branch, index) => (
                <BranchItem key={index} name={branch} imageSrc={p9} />
              ))}
            </div>
          </div>
        </div>
      </section>


<div className="container">
  <div className="pricing-card-container">
    {/* Basic Plan */}
    <div className="card">
      <div className="upper-part">
        <h2>Basic Plan</h2>
        <small>Ideal for small faculties or departments</small>
        <div className="discount">
          <small>EGP 199</small>
          <span>SAVE 50%</span>
        </div>
        <div className="price">
          {/* <h3><i className="fa-solid fa-pound-sign"></i><span>99</span>/mo</h3> */}
          <h3><i>EGP</i><span>99</span>/mo</h3>

        </div>
        <button>Add to cart</button>
        <p>Valid for 1 month</p>
      </div>
      <hr />
      <div className="feature">
        <h4>Included Features</h4>
        <ul>
          <li><i className="fa-solid fa-check yellow"></i>Up to <span>2</span> user accounts</li>
          <li><i className="fa-solid fa-check"></i>Manage student groups & assistants</li>
          <li><i className="fa-solid fa-check"></i>Room & subject management</li>
          <li><i className="fa-solid fa-check"></i>Automated exam & invigilator distribution</li>
          <li><i className="fa-solid fa-check yellow"></i>Limited support for 7 days</li>
        </ul>
        <div className="dropdown-btn">
          <a href="#">See all features<i className="fa-solid fa-chevron-down"></i></a>
        </div>
      </div>
    </div>

    {/* Pro Plan */}
    <div className="card card-2">
      <span className="badge">Most Popular</span>
      <div className="upper-part">
        <h2>Pro Plan</h2>
        <small>Perfect for medium-sized faculties</small>
        <div className="discount">
          <small>EGP 299</small>
          <span>SAVE 33%</span>
        </div>
        <div className="price">
          {/* <h3><i className="fa-solid fa-pound-sign"></i><span>199</span>/mo</h3> */}
          <h3><i>EGP</i><span>199</span>/mo</h3>

        </div>
        <span className="free">All Basic Features Included</span>
        <button>Add to cart</button>
        <p>Valid for 1 month</p>
      </div>
      <hr />
      <div className="feature">
        <h4>Included Features</h4>
        <ul>
          <li><i className="fa-solid fa-check"></i>Up to <span>3</span> user accounts</li>
          <li><i className="fa-solid fa-check"></i>Full system support for 1 month</li>
          <li><i className="fa-solid fa-check"></i>Downloadable exam reports</li>
          <li><i className="fa-solid fa-check"></i>Performance updates & improvements</li>
          <li><i className="fa-solid fa-check"></i>All features from Basic Plan</li>
        </ul>
        <div className="dropdown-btn">
          <a href="#">See all features<i className="fa-solid fa-chevron-down"></i></a>
        </div>
      </div>
    </div>

    {/* Ultimate Plan */}
    <div className="card card-3">
      <div className="upper-part">
        <h2>Ultimate Plan</h2>
        <small>Designed for large faculties or universities</small>
        <div className="discount">
          <small>EGP 499</small>
          <span>SAVE 30%</span>
        </div>
        <div className="price">
          {/* <h3><i className="fa-solid fa-pound-sign"></i><span>349</span>/mo</h3> */}
          <h3><i>EGP</i><span>349</span>/mo</h3>

        </div>
        <span className="free">+3 Months Subscription</span>
        <button>Add to cart</button>
        <p>Billed every 3 months</p>
      </div>
      <hr />
      <div className="feature">
        <h4>Included Features</h4>
        <ul>
          <li><i className="fa-solid fa-check"></i>Up to <span>5</span> user accounts</li>
          <li><i className="fa-solid fa-check"></i>Unlimited technical support</li>
          <li><i className="fa-solid fa-check"></i>Training materials & tutorials</li>
          <li><i className="fa-solid fa-check"></i>Custom feature requests (by agreement)</li>
          <li><i className="fa-solid fa-check"></i>Export to Excel & PDF</li>
        </ul>
        <div className="dropdown-btn">
          <a href="#">See all features<i className="fa-solid fa-chevron-down"></i></a>
        </div>
      </div>
    </div>
  </div>
</div>




      {/* Footer Section */}
      {/* Footer Section */}
      <footer className="footer">
        <div className="container">
          <div className="footer-content">
            <div className="part1">
              <h1>The Content</h1>
              <p>
                Subscribe to <strong>Easy Tutorials</strong> YouTube Channel to watch more videos on UI Designing, Web Designing, Digital Marketing, and Graphics Designing. Press the bell icon to get notifications immediately when we upload new videos.
              </p>
              <button className="contact-btn">Contact Us</button>
            </div>
            <form className="footer-form">
              <input type="text" placeholder="Your Name" />
              <input type="email" placeholder="Your Email" />
              <textarea placeholder="Your Message"></textarea>
              <button type="submit" className="send-btn">SEND</button>
            </form>
          </div>
        </div>
      </footer>


      {/* End Section */}
      {/* Footer Section */}
      <footer className="footer-end">
        <div className="container">
          <p className="created-by">
            Created by <strong>Mohamed Yasser</strong>
          </p>
          <p className="copyright">
            &copy; {new Date().getFullYear()} All Rights Reserved For EELU
          </p>
        </div>
      </footer>

    </div>
  );
};

export default Home;
