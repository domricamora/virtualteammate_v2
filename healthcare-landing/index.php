<?php
$page_title       = 'Healthcare Practice Efficiency Quiz | Virtual Teammate';
$page_description = 'Take the 2-minute Virtual Teammate efficiency quiz for healthcare practice owners. See how many hours and how much weekly revenue you could reclaim by delegating admin to a HIPAA-certified virtual teammate.';
$og_title         = 'How Much Time & Revenue Is Admin Costing Your Practice?';
$og_description   = 'A 2-minute quiz for healthcare practice owners — get your efficiency tier, estimated hours saved, and recoverable weekly revenue, plus a downloadable report.';
$canonical        = 'https://virtualteammate.com/healthcare-landing/';
$home_base        = '../';
$breadcrumbs      = [
  ['name' => 'Home',       'url' => '/'],
  ['name' => 'Healthcare', 'url' => '/healthcare-landing/'],
];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';
?>

<style>
/* ── Healthcare quiz landing — scoped styles (dark/gold theme) ── */
.hcq-hero{padding:56px 20px 22px;max-width:1100px;margin:0 auto;}
.hcq-hero-grid{display:grid;grid-template-columns:1.15fr .85fr;gap:36px;align-items:center;}
.hcq-hero .sec-lbl{display:inline-flex;}
.hcq-hero h1{font-size:40px;line-height:1.12;letter-spacing:-.5px;margin:14px 0 14px;color:#fff;}
.hcq-hero h1 em{color:var(--gold);font-style:normal;}
.hcq-hero p{font-size:17px;line-height:1.65;color:var(--text-soft,#c9c8e2);margin:0;}
.hcq-hero-photo img{width:100%;max-width:380px;height:auto;display:block;margin:0 auto;filter:drop-shadow(0 26px 50px rgba(0,0,0,.45));}

.healthcare-quiz-wrap{max-width:760px;margin:0 auto 90px;padding:0 20px;}
.quiz-container{
  background:var(--glass-bg,rgba(255,255,255,.1));
  border:1px solid var(--glass-border,rgba(255,255,255,.28));
  border-radius:22px;padding:34px;
  backdrop-filter:var(--glass-blur,blur(18px));-webkit-backdrop-filter:var(--glass-blur,blur(18px));
  box-shadow:0 30px 80px -40px rgba(0,0,0,.6);
}
.progress-bar{height:8px;background:rgba(255,255,255,.12);border-radius:99px;overflow:hidden;margin-bottom:28px;}
.progress{height:100%;width:0;background:linear-gradient(90deg,var(--gold,#dfa949),#f5d27a);border-radius:99px;transition:width .35s ease;}
#quiz h2{font-size:23px;line-height:1.3;color:#fff;margin:0 0 22px;font-weight:800;}
.option{
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.14);
  border-radius:13px;padding:15px 18px;margin-bottom:12px;color:rgba(255,255,255,.9);
  cursor:pointer;font-weight:600;font-size:15px;transition:border-color .2s,background .2s,transform .2s;
}
.option:hover{border-color:var(--gold,#dfa949);background:rgba(223,169,73,.14);color:#fff;transform:translateY(-1px);}

.result-box{text-align:center;color:#fff;}
.result-box h2{color:var(--gold,#dfa949);font-size:26px;margin:0 0 14px;}
.result-box p{color:var(--text-soft,#c9c8e2);font-size:15.5px;line-height:1.65;margin:8px 0;}
.result-box p strong{color:#fff;}
.badge{display:inline-block;background:linear-gradient(135deg,var(--gold,#dfa949),#f5d27a);color:#1a1535;font-weight:800;letter-spacing:.4px;padding:10px 20px;border-radius:99px;margin:18px 0 6px;}
.result-box input{
  width:100%;max-width:380px;box-sizing:border-box;margin:8px auto 0;display:block;
  background:rgba(255,255,255,.06);border:1px solid var(--glass-border,rgba(255,255,255,.28));
  border-radius:12px;padding:13px 16px;color:#fff;font-family:inherit;font-size:14.5px;outline:none;
}
.result-box input::placeholder{color:rgba(255,255,255,.5);}
.result-box input:focus{border-color:var(--gold,#dfa949);}
.result-box button{
  background:rgba(255,255,255,.07);border:1px solid var(--glass-border,rgba(255,255,255,.28));
  color:#fff;font-family:inherit;font-weight:700;font-size:14.5px;cursor:pointer;
  padding:13px 24px;border-radius:12px;transition:border-color .2s,background .2s;
}
.result-box button:hover{border-color:var(--gold,#dfa949);background:rgba(255,255,255,.12);}
.result-box .cta{
  background:linear-gradient(135deg,var(--gold,#dfa949),#f5d27a);color:#1a1535;border:0;
  font-weight:800;font-size:15.5px;padding:15px 28px;border-radius:12px;margin-top:8px;
}
.result-box .cta:hover{filter:brightness(1.05);background:linear-gradient(135deg,var(--gold,#dfa949),#f5d27a);}

@media (max-width:768px){
  .hcq-hero{padding:36px 18px 16px;}
  .hcq-hero-grid{grid-template-columns:1fr;gap:18px;text-align:center;}
  .hcq-hero .sec-lbl{justify-content:center;}
  .hcq-hero h1{font-size:30px;}
  .hcq-hero-photo{order:-1;}
  .hcq-hero-photo img{max-width:240px;}
  .quiz-container{padding:24px 20px;}
}
</style>

<main>
  <header class="hcq-hero reveal">
    <div class="hcq-hero-grid">
      <div class="hcq-hero-copy">
        <div class="sec-lbl"><i class="fa-solid fa-stethoscope"></i> Practice Efficiency Quiz</div>
        <h1>How Much Time &amp; Revenue Is <em>Admin Costing</em> Your Practice?</h1>
        <p>Answer 6 quick questions to get your efficiency tier, an estimate of the hours you could reclaim each week, and the weekly revenue hiding in your admin workload &mdash; plus a downloadable report. Takes about 2 minutes.</p>
      </div>
      <div class="hcq-hero-photo">
        <img src="<?= $home_base ?>images/healthcare-quiz.webp" alt="Smiling HIPAA-certified healthcare virtual assistant wearing a headset, ready to take admin work off your practice" width="1200" height="1536" loading="eager">
      </div>
    </div>
  </header>

  <div class="healthcare-quiz-wrap">
    <div class="quiz-container">
      <div class="progress-bar">
        <div class="progress" id="progress"></div>
      </div>
      <div id="quiz"></div>
    </div>
  </div>

  <!-- TRUST STATS -->
  <div class="svc-stats reveal">
    <div class="svc-stat"><div class="svc-stat-num">73%</div><div class="svc-stat-lbl">Lower Staffing Cost</div></div>
    <div class="svc-stat"><div class="svc-stat-num">100%</div><div class="svc-stat-lbl">HIPAA-Certified</div></div>
    <div class="svc-stat"><div class="svc-stat-num">1&ndash;2</div><div class="svc-stat-lbl">Weeks to Live</div></div>
    <div class="svc-stat"><div class="svc-stat-num">30</div><div class="svc-stat-lbl">Day Right-Fit Promise</div></div>
  </div>

  <div class="divider"></div>

  <!-- WHY IT MATTERS -->
  <section class="svc-split">
    <div class="reveal">
      <div class="sec-lbl"><i class="fa-solid fa-triangle-exclamation"></i> The Hidden Cost of Admin</div>
      <h2 class="svc-h2">Every Hour on Paperwork Is an Hour <em>Away From Patients</em></h2>
      <p class="svc-p">Scheduling, insurance verification, charting, callbacks, claims follow-up &mdash; the back office quietly eats the time you meant to spend on care and on growing the practice. It doesn&rsquo;t show up on a P&amp;L, but you feel it in late nights, slow follow-ups and stalled revenue.</p>
      <ul class="svc-checks">
        <li><i class="fa-solid fa-check"></i><span><strong>Charting after hours</strong> instead of clocking out with your team.</span></li>
        <li><i class="fa-solid fa-check"></i><span><strong>Missed follow-ups</strong> that let patients &mdash; and revenue &mdash; slip away.</span></li>
        <li><i class="fa-solid fa-check"></i><span><strong>Aging claims and unverified benefits</strong> tying up cash you&rsquo;ve already earned.</span></li>
        <li><i class="fa-solid fa-check"></i><span><strong>A front desk underwater</strong> on calls, intake and inbox &mdash; every single day.</span></li>
      </ul>
      <p class="svc-p">The quiz above puts a number on it. A HIPAA-certified Virtual Teammate takes it off your plate.</p>
    </div>
    <div class="svc-side-img reveal d2">
      <img src="<?= $home_base ?>images/photos/healthcare/How-Our-Virtual-Teammate-Help-Reduce-Costs.webp" alt="Healthcare practice reclaiming hours and revenue with a Virtual Teammate" loading="lazy"/>
    </div>
  </section>

  <div class="divider"></div>

  <!-- WHAT THEY HANDLE -->
  <section class="svc-bens">
    <div class="reveal" style="text-align:center;">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-list-check"></i> What They Take Off Your Plate</div>
      <h2 class="svc-h2">One Teammate. <em>The Whole Back Office.</em></h2>
      <p class="sec-sub" style="max-width:700px;margin:0 auto;">HIPAA-certified, EHR-trained, and matched to your time zone &mdash; your Virtual Teammate owns the repeatable work so your clinical team can focus on patients.</p>
    </div>
    <div class="svc-bens-grid">
      <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-calendar-check"></i></span><h3>Scheduling &amp; Intake</h3><p>Booking, confirmations, reschedules, recalls and new-patient intake &mdash; calendars stay full and gaps get filled.</p></div>
      <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-file-invoice-dollar"></i></span><h3>Billing &amp; RCM</h3><p>Claims, payment posting, AR follow-up and denials worked daily so revenue lands faster and cleaner.</p></div>
      <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-clipboard-check"></i></span><h3>Insurance &amp; Prior Auth</h3><p>Eligibility checks, benefit verification and prior authorizations completed before the visit, not after.</p></div>
      <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-pen-clip"></i></span><h3>Scribing &amp; Documentation</h3><p>Real-time charting inside your EHR so notes are done at the visit &mdash; not at midnight.</p></div>
      <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-headset"></i></span><h3>Patient Calls &amp; Follow-Up</h3><p>Inbound and outbound calls, reminders and follow-ups handled in your tone and time zone.</p></div>
      <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-inbox"></i></span><h3>Inbox &amp; Records</h3><p>Portal messages, faxes, referrals and records management kept current and never backlogged.</p></div>
    </div>
  </section>

  <div class="divider"></div>

  <!-- FAQ -->
  <section class="sec" id="faq" style="padding-top:60px;">
    <div class="reveal" style="text-align:center;">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-circle-question"></i> FAQ</div>
      <h2 class="svc-h2">Questions Practice Owners <em>Ask Us First</em></h2>
    </div>
    <div class="faq-grid">
      <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-shield-halved"></i> Are your healthcare teammates HIPAA certified?</div><div class="faq-a">Yes. Every healthcare and dental teammate completes HIPAA training and certification before placement, works in encrypted environments only, and is BAA-compatible.</div></div>
      <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-laptop-medical"></i> Do they know my EHR?</div><div class="faq-a">We match on tool fluency. Our teammates work daily in Epic, Cerner, athenahealth, eClinicalWorks, Dentrix, Eaglesoft and more &mdash; and we confirm the fit during selection.</div></div>
      <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-clock"></i> How fast can someone start?</div><div class="faq-a">Most practices receive a curated shortlist within days and have their teammate live in 1&ndash;2 weeks &mdash; every placement backed by the 30-Day Right-Fit Promise.</div></div>
      <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> How much does it cost?</div><div class="faq-a">Transparent flat-rate pricing &mdash; typically 60&ndash;73% less than an equivalent in-house hire once salary, benefits, payroll tax and overhead are included. No recruiter fees, no long-term lock-in.</div></div>
    </div>
  </section>

  <!-- CLOSING CTA -->
  <section class="svc-cta">
    <h2>See Your Score? <em style="color:var(--gold);font-style:normal;">Now Reclaim the Hours.</em></h2>
    <p>Book a free 15-minute consultation and we&rsquo;ll map exactly which tasks to delegate first &mdash; or start your practice staffing audit. No commitment, covered by the 30-Day Right-Fit Promise.</p>
    <div class="svc-cta-btns">
      <a href="https://meetings.hubspot.com/chris4273/sales-discovery-round-robin" target="_blank" rel="noopener" class="btn-primary">Book a Free 15-Min Consultation <i class="fa-solid fa-arrow-right"></i></a>
      <a href="#cta-book" data-cta-intent="practice-audit" class="btn-glass">Book My Practice Staffing Audit <i class="fa-solid fa-clipboard-check"></i></a>
    </div>
  </section>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
const quizData = [
{
question: "Q1. How many hours per week do you personally spend on scheduling?",
options: [
{text:"0–5 hours", points:0},
{text:"6–10 hours", points:5},
{text:"11–20 hours", points:10},
{text:"20+ hours", points:15}
]
},
{
question: "Q2. How often do patient follow-ups get delayed?",
options: [
{text:"Never", points:0},
{text:"Rarely", points:5},
{text:"Sometimes", points:10},
{text:"Often", points:15}
]
},
{
question: "Q3. How many patient calls/emails daily?",
options: [
{text:"0–20", points:0},
{text:"21–50", points:5},
{text:"51–100", points:10},
{text:"100+", points:15}
]
},
{
question: "Q4. Weekly documentation time?",
options: [
{text:"0–5 hours", points:0},
{text:"6–10 hours", points:5},
{text:"11–20 hours", points:10},
{text:"20+ hours", points:15}
]
},
{
question: "Q5. Admin backlogs slow workflow?",
options: [
{text:"Never", points:0},
{text:"Rarely", points:5},
{text:"Sometimes", points:10},
{text:"Often", points:15}
]
},
{
question: "Q6. Choose your superpower:",
options: [
{text:"More patient time", points:5},
{text:"More free time", points:5},
{text:"Less stress", points:5},
{text:"Grow my clinic", points:5}
]
}
];

let currentQuestion = 0;
let totalPoints = 0;

function loadQuestion() {
    const quiz = document.getElementById("quiz");
    const progress = document.getElementById("progress");

    progress.style.width = ((currentQuestion / quizData.length) * 100) + "%";

    if(currentQuestion >= quizData.length){
        showResults();
        return;
    }

    const q = quizData[currentQuestion];
    quiz.innerHTML = `<h2>${q.question}</h2>`;

    q.options.forEach(opt=>{
        const btn = document.createElement("div");
        btn.classList.add("option");
        btn.innerText = opt.text;
        btn.onclick = ()=>{
            totalPoints += opt.points;
            currentQuestion++;
            loadQuestion();
        }
        quiz.appendChild(btn);
    });
}

function showResults(){
    const quiz = document.getElementById("quiz");
    document.getElementById("progress").style.width = "100%";

    let tier = "";
    let message = "";
    let hoursSaved = Math.floor((totalPoints / 80) * 20);
    let revenuePotential = hoursSaved * 150;

    if(totalPoints <= 20){
        tier = "Minimal Need";
        message = "Your systems are solid, but small delegation improvements could unlock more freedom.";
    } else if(totalPoints <= 40){
        tier = "Moderate Need";
        message = "You have optimization opportunities. Delegating admin could reclaim serious time.";
    } else if(totalPoints <= 60){
        tier = "High Need";
        message = "You're overloaded. A Virtual Teammate could dramatically improve operations.";
    } else {
        tier = "Urgent Need";
        message = "Your clinic is likely operating below its potential due to admin overload.";
    }

    quiz.innerHTML = `
        <div class="result-box">
            <h2>Your Result: ${tier}</h2>
            <p><strong>Total Score:</strong> ${totalPoints} points</p>
            <p>${message}</p>
            <p><strong>Estimated Time Saved:</strong> ${hoursSaved} hours/week ⏳</p>
            <p><strong>Potential Revenue Recovered:</strong> $${revenuePotential}/week 💰</p>
            <div class="badge">🏆 Certified Delegator</div>
            <br>
            <button class="cta" onclick="window.open('https://meetings.hubspot.com/chris4273/sales-discovery-round-robin','_blank')">
                Book a Free 15-min Consultation →
            </button>
            <br><br>
            <input type="email" id="userEmail" placeholder="Enter email for detailed report">
            <br>

             <input type="phone" id="userPhone" placeholder="Enter phone for detailed report">
             <br>
            <br>
            <button onclick="generatePDF('${tier}', ${hoursSaved}, ${revenuePotential})">
                Download My Detailed Report (PDF)
            </button>
            <br><br>
            <button onclick="restartQuiz()">Retake Quiz</button>
        </div>
    `;
}
function generatePDF(tier, hoursSaved, revenuePotential){
    const email = document.getElementById("userEmail").value;
     const phone = document.getElementById("userPhone").value;
    const leadsource = "Virtual Teammate Quiz - Healthcare Owner";
     if(!email && !phone){
        alert("Please enter either your email or phone number before downloading your report.");
        return;
    }

    // 🔹 Submit to HubSpot
   submitToHubSpot(email, phone, leadsource);

    // 🔹 Submit to the Virtual Teammate lead database (portal Leads page)
   submitToLeadDB(email, phone, tier, hoursSaved, revenuePotential);

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.setFontSize(18);
    doc.text("Virtual Teammate Efficiency Report", 20, 20);

    doc.setFontSize(12);
    doc.text(`Generated for: ${email}`, 20, 30);
    doc.text(`Assessment Tier: ${tier}`, 20, 40);
    doc.text(`Total Score: ${totalPoints}`, 20, 50);
    doc.text(`Estimated Time Saved: ${hoursSaved} hours/week`, 20, 60);
    doc.text(`Potential Revenue Recovered: $${revenuePotential}/week`, 20, 70);

    doc.line(20, 75, 190, 75);

    doc.setFontSize(14);
    doc.text("Detailed Analysis", 20, 85);

    doc.setFontSize(11);
    let analysisText = `
Based on your responses, your clinic is currently losing approximately ${hoursSaved} hours per week to administrative tasks.

This represents an estimated $${revenuePotential} in recoverable weekly revenue opportunity.

Key Optimization Areas:
• Scheduling efficiency
• Follow-up automation
• Documentation delegation
• Workflow optimization
• Patient communication systems

Recommended Next Step:
Book a 15-minute consultation to design your delegation strategy.
    `;

    doc.text(analysisText, 20, 95, { maxWidth: 170 });

    doc.setFontSize(10);
    doc.text("Confidential - Virtual Teammate Assessment", 20, 280);

    doc.save("Virtual_Teammate_Report.pdf");
}

function restartQuiz(){
    currentQuestion = 0;
    totalPoints = 0;
    loadQuestion();
}
// Fire-and-forget POST to the site lead endpoint (saves to the portal Leads DB).
const LEAD_URL = "<?= $home_base ?>lead.php";
function submitToLeadDB(email, phone, tier, hoursSaved, revenuePotential){
    const fd = new URLSearchParams();
    fd.append("email", email || "");
    fd.append("phone", phone || "");
    fd.append("source", "Healthcare Efficiency Quiz");
    fd.append("form", "healthcare-quiz");
    fd.append("message",
        "Quiz result: " + tier + " (" + totalPoints + " pts). " +
        "Est. " + hoursSaved + " hrs/week reclaimable, ~$" + revenuePotential + "/week in recoverable revenue.");
    fetch(LEAD_URL, { method: "POST", body: fd, credentials: "same-origin" })
        .then(function(r){ if(!r.ok){ console.error("Lead DB submission error"); } })
        .catch(function(e){ console.error("Lead DB submission failed:", e); });
}
function submitToHubSpot(email, phone, leadsource){
       const portalId = "46221241";
    const formId = "e744deab-314f-47a5-986d-39c87e47646f";


    fetch(`https://api.hsforms.com/submissions/v3/integration/submit/${portalId}/${formId}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            fields: [
               { name: "email", value: email },
                { name: "phone", value: phone },
                { name: "leadsource", value: leadsource }
            ],
            context: {
                pageUri: window.location.href,
                pageName: "Virtual Teammate Quiz"
            }
        })
    })
    .then(response => {
        if(response.ok){
            console.log("Lead successfully sent to HubSpot");
        } else {
            console.error("HubSpot submission error");
        }
    })
    .catch(error => console.error("Submission failed:", error));
}


loadQuestion();
</script>

<?php include __DIR__ . '/../includes/book-modal.php'; /* #cta-book scheduler for the CTA above */ ?>
<?php $hide_lead_band = true; /* the quiz is the page's lead capture */ ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
