<?php
$page_title       = 'Business Efficiency Quiz — How Many Hours Could You Reclaim? | Virtual Teammate';
$page_description = 'Take the 2-minute Virtual Teammate efficiency quiz for business owners. See how many hours and how much weekly revenue you could reclaim by delegating repetitive admin, email, scheduling and reporting to a vetted virtual teammate.';
$og_title         = 'How Much Is Busywork Costing Your Business?';
$og_description   = 'A 2-minute quiz for business owners — get your efficiency tier, estimated hours saved, and recoverable weekly revenue, plus a downloadable report.';
$canonical        = 'https://virtualteammate.com/business-landing/';
$home_base        = '../';
$breadcrumbs      = [
  ['name' => 'Home',     'url' => '/'],
  ['name' => 'Business', 'url' => '/business-landing/'],
];
include __DIR__ . '/../includes/head.php';
include __DIR__ . '/../includes/nav.php';
?>

<style>
/* ── Business quiz landing — scoped styles (dark/gold theme) ── */
.bzq-hero{padding:56px 20px 22px;max-width:1100px;margin:0 auto;}
.bzq-hero-grid{display:grid;grid-template-columns:1.15fr .85fr;gap:36px;align-items:center;}
.bzq-hero .sec-lbl{display:inline-flex;}
.bzq-hero h1{font-size:40px;line-height:1.12;letter-spacing:-.5px;margin:14px 0 14px;color:#fff;}
.bzq-hero h1 em{color:var(--gold);font-style:normal;}
.bzq-hero p{font-size:17px;line-height:1.65;color:var(--text-soft,#c9c8e2);margin:0;}
.bzq-hero-photo img{width:100%;max-width:380px;height:auto;display:block;margin:0 auto;filter:drop-shadow(0 26px 50px rgba(0,0,0,.45));}

.business-quiz-wrap{max-width:760px;margin:0 auto 90px;padding:0 20px;}
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
  display:flex;align-items:center;
  background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.14);
  border-radius:13px;padding:15px 18px;margin-bottom:12px;color:rgba(255,255,255,.9);
  cursor:pointer;font-weight:600;font-size:15px;transition:border-color .2s,background .2s,transform .2s;
}
.option:hover{border-color:var(--gold,#dfa949);background:rgba(223,169,73,.14);color:#fff;transform:translateY(-1px);}
#quiz > button{
  margin-top:6px;background:linear-gradient(135deg,var(--gold,#dfa949),#f5d27a);color:#1a1535;border:0;
  font-family:inherit;font-weight:800;font-size:15px;cursor:pointer;padding:14px 28px;border-radius:12px;
}
#quiz > button:hover{filter:brightness(1.05);}

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
  padding:13px 24px;border-radius:12px;transition:border-color .2s,background .2s;margin-top:8px;
}
.result-box button:hover{border-color:var(--gold,#dfa949);background:rgba(255,255,255,.12);}
.result-box .cta{
  background:linear-gradient(135deg,var(--gold,#dfa949),#f5d27a);color:#1a1535;border:0;
  font-weight:800;font-size:15.5px;padding:15px 28px;border-radius:12px;margin-top:8px;
}
.result-box .cta:hover{filter:brightness(1.05);background:linear-gradient(135deg,var(--gold,#dfa949),#f5d27a);}

@media (max-width:768px){
  .bzq-hero{padding:36px 18px 16px;}
  .bzq-hero-grid{grid-template-columns:1fr;gap:18px;text-align:center;}
  .bzq-hero .sec-lbl{justify-content:center;}
  .bzq-hero h1{font-size:30px;}
  .bzq-hero-photo{order:-1;}
  .bzq-hero-photo img{max-width:240px;}
  .quiz-container{padding:24px 20px;}
}
</style>

<main>
  <header class="bzq-hero reveal">
    <div class="bzq-hero-grid">
      <div class="bzq-hero-copy">
        <div class="sec-lbl"><i class="fa-solid fa-briefcase"></i> Business Efficiency Quiz</div>
        <h1>How Much Is <em>Busywork Costing</em> Your Business?</h1>
        <p>Answer 8 quick questions to get your efficiency tier, an estimate of the hours you could reclaim each week, and the weekly revenue trapped in repetitive admin, email and reporting &mdash; plus a downloadable report. Takes about 2 minutes.</p>
      </div>
      <div class="bzq-hero-photo">
        <img src="<?= $home_base ?>images/business-quiz.webp" alt="Smiling business virtual assistant in a suit wearing a headset, ready to take repetitive work off your plate" width="1200" height="1536" loading="eager">
      </div>
    </div>
  </header>

  <div class="business-quiz-wrap">
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
    <div class="svc-stat"><div class="svc-stat-num">12+</div><div class="svc-stat-lbl">Countries Sourced</div></div>
    <div class="svc-stat"><div class="svc-stat-num">1&ndash;2</div><div class="svc-stat-lbl">Weeks to Live</div></div>
    <div class="svc-stat"><div class="svc-stat-num">30</div><div class="svc-stat-lbl">Day Right-Fit Promise</div></div>
  </div>

  <div class="divider"></div>

  <!-- WHY IT MATTERS -->
  <section class="svc-split">
    <div class="reveal">
      <div class="sec-lbl"><i class="fa-solid fa-triangle-exclamation"></i> The Hidden Cost of Busywork</div>
      <h2 class="svc-h2">Every Hour on Admin Is an Hour <em>Not Spent Growing</em></h2>
      <p class="svc-p">Inbox triage, scheduling, data entry, lead tracking, reporting &mdash; the repetitive work quietly swallows the time you meant to spend on clients, strategy and revenue. It doesn&rsquo;t feel like much in the moment, but it&rsquo;s the difference between running your business and being run by it.</p>
      <ul class="svc-checks">
        <li><i class="fa-solid fa-check"></i><span><strong>Drowning in email</strong> instead of closing the next deal.</span></li>
        <li><i class="fa-solid fa-check"></i><span><strong>Growth initiatives postponed</strong> because admin always comes first.</span></li>
        <li><i class="fa-solid fa-check"></i><span><strong>Leads slipping through the cracks</strong> from slow or missed follow-up.</span></li>
        <li><i class="fa-solid fa-check"></i><span><strong>Reporting and data entry</strong> eating the hours you should spend on clients.</span></li>
      </ul>
      <p class="svc-p">The quiz above puts a number on it. A vetted Virtual Teammate hands those hours back.</p>
    </div>
    <div class="svc-side-img reveal d2">
      <img src="<?= $home_base ?>images/photos/business/email-management.webp" alt="Business owner reclaiming hours by delegating admin to a Virtual Teammate" loading="lazy"/>
    </div>
  </section>

  <div class="divider"></div>

  <!-- WHAT THEY HANDLE -->
  <section class="svc-bens">
    <div class="reveal" style="text-align:center;">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-list-check"></i> What They Take Off Your Plate</div>
      <h2 class="svc-h2">One Teammate. <em>The Whole Back Office.</em></h2>
      <p class="sec-sub" style="max-width:700px;margin:0 auto;">Multi-stage vetted, trained on your tools, and matched to your time zone &mdash; your Virtual Teammate owns the repeatable work so you can focus on clients and growth.</p>
    </div>
    <div class="svc-bens-grid">
      <div class="svc-ben reveal d1"><span class="ico-circle lg"><i class="fa-solid fa-clipboard"></i></span><h3>Executive &amp; Admin</h3><p>Inbox and calendar management, scheduling, travel, document control and project coordination &mdash; your day, handled.</p></div>
      <div class="svc-ben reveal d2"><span class="ico-circle lg"><i class="fa-solid fa-bullseye"></i></span><h3>Sales &amp; Lead Gen</h3><p>Prospect lists, outbound outreach, lead qualification and appointment setting that keep your pipeline full.</p></div>
      <div class="svc-ben reveal d3"><span class="ico-circle lg"><i class="fa-solid fa-bullhorn"></i></span><h3>Marketing &amp; Social</h3><p>Social posting, content production, email campaigns and ad coordination &mdash; shipped on schedule, not half-finished.</p></div>
      <div class="svc-ben reveal d4"><span class="ico-circle lg"><i class="fa-solid fa-sack-dollar"></i></span><h3>Finance &amp; Bookkeeping</h3><p>QuickBooks/Xero bookkeeping, invoicing, AR follow-up and expense management so cash lands faster.</p></div>
      <div class="svc-ben reveal d5"><span class="ico-circle lg"><i class="fa-solid fa-headset"></i></span><h3>Customer Support</h3><p>Tier-1 support, live chat, ticket triage and onboarding follow-up in your tone and time zone.</p></div>
      <div class="svc-ben reveal d6"><span class="ico-circle lg"><i class="fa-solid fa-diagram-project"></i></span><h3>CRM &amp; Reporting</h3><p>CRM hygiene, pipeline updates, dashboards and KPI reporting so your data is clean and your forecasts are real.</p></div>
    </div>
  </section>

  <div class="divider"></div>

  <!-- FAQ -->
  <section class="sec" id="faq" style="padding-top:60px;">
    <div class="reveal" style="text-align:center;">
      <div class="sec-lbl" style="justify-content:center;display:inline-flex;"><i class="fa-solid fa-circle-question"></i> FAQ</div>
      <h2 class="svc-h2">Questions Business Owners <em>Ask Us First</em></h2>
    </div>
    <div class="faq-grid">
      <div class="faq-item reveal d1"><div class="faq-q"><i class="fa-solid fa-briefcase"></i> What does a business virtual assistant do?</div><div class="faq-a">A business VA handles the repeatable work across admin, sales, marketing, finance and customer service &mdash; from inbox and calendar management to lead generation, bookkeeping and reporting. You decide the function; we match a vetted teammate trained on your tools.</div></div>
      <div class="faq-item reveal d2"><div class="faq-q"><i class="fa-solid fa-screwdriver-wrench"></i> Which tools do they work in?</div><div class="faq-a">Our teammates work daily in HubSpot, Salesforce, Pipedrive, QuickBooks, Xero, Google Workspace, Microsoft 365 and the rest of the common stack &mdash; plus the project and outreach tools your team already runs. We match for tool fluency during selection.</div></div>
      <div class="faq-item reveal d3"><div class="faq-q"><i class="fa-solid fa-clock"></i> How fast can someone start?</div><div class="faq-a">Most clients receive a curated shortlist within days and have their teammate live in 1&ndash;2 weeks &mdash; every placement backed by the 30-Day Right-Fit Promise.</div></div>
      <div class="faq-item reveal d4"><div class="faq-q"><i class="fa-solid fa-sack-dollar"></i> How much does it cost?</div><div class="faq-a">Transparent flat-rate pricing &mdash; typically 60&ndash;73% less than an equivalent in-house hire once salary, benefits, payroll tax and overhead are included. No recruiter fees, no long-term lock-in.</div></div>
    </div>
  </section>

  <!-- CLOSING CTA -->
  <section class="svc-cta">
    <h2>See Your Score? <em style="color:var(--gold);font-style:normal;">Now Reclaim the Hours.</em></h2>
    <p>Book a free 15-minute consultation and we&rsquo;ll map exactly which tasks to delegate first &mdash; or buy back your company&rsquo;s time with a vetted teammate. No commitment, covered by the 30-Day Right-Fit Promise.</p>
    <div class="svc-cta-btns">
      <a href="https://meetings.hubspot.com/chris4273/sales-discovery-round-robin" target="_blank" rel="noopener" class="btn-primary">Book a Free 15-Min Consultation <i class="fa-solid fa-arrow-right"></i></a>
      <a href="<?= $home_base ?>business/#cta-buyback" data-cta-intent="buyback" class="btn-glass">Buy Back Your Company&rsquo;s Time <i class="fa-solid fa-clock"></i></a>
    </div>
  </section>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
const quizData = [
{
question: "Q1. How many hours per week do you spend on repetitive tasks?",
options: [
{text:"0–5 hours", points:0},
{text:"6–10 hours", points:5},
{text:"11–20 hours", points:10},
{text:"20+ hours", points:15}
]
},
{
question: "Q2. How often do small tasks delay your important projects?",
options: [
{text:"Never", points:0},
{text:"Sometimes", points:5},
{text:"Often", points:10},
{text:"Always", points:15}
]
},
{
question: "Q3. How much time do you spend on email management daily?",
options: [
{text:"0–1 hour", points:0},
{text:"1–2 hours", points:5},
{text:"2–4 hours", points:10},
{text:"4+ hours", points:15}
]
},
{
question: "Q4. How often do you postpone growth initiatives due to admin work?",
options: [
{text:"Never", points:0},
{text:"Sometimes", points:5},
{text:"Often", points:10},
{text:"Always", points:15}
]
},
{
question: "Q5. How many hours per week do you spend on social media, lead tracking, or reporting?",
options: [
{text:"0–5 hours", points:0},
{text:"6–10 hours", points:5},
{text:"11–20 hours", points:10},
{text:"20+ hours", points:15}
]
},
{
question: "Q6. How often do missed deadlines or errors cost your business money?",
options: [
{text:"Never", points:0},
{text:"Sometimes", points:5},
{text:"Often", points:10},
{text:"Always", points:15}
]
},
{
question: "Q7. Which tasks cause you the most stress? (Select all that apply)",
multiSelect: true,
options: [
{text: "Email follow-ups", points:5},
{text: "Scheduling", points:5},
{text: "Data entry", points:5},
{text: "Social media", points:5},
{text: "Reporting", points:5}
]
},
{
question: "Q8 Choose your superpower:",
options: [
{text:"More client time", points:5},
{text:"More free time", points:5},
{text:"Less stress", points:5},
{text:"Grow my revenue faster", points:5}
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

    // Multi-select question handling
    if(q.multiSelect){
        const form = document.createElement('div');

        q.options.forEach((opt, idx) => {
            const label = document.createElement('label');
            label.classList.add('option');

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.dataset.points = opt.points;
            checkbox.style.marginRight = '8px';
            checkbox.id = `opt_${currentQuestion}_${idx}`;

            label.appendChild(checkbox);
            const span = document.createElement('span');
            span.innerText = opt.text;
            label.appendChild(span);

            form.appendChild(label);
        });

        const nextBtn = document.createElement('button');
        nextBtn.innerText = 'Next';
        nextBtn.onclick = () => {
            const checkboxes = form.querySelectorAll('input[type="checkbox"]');
            let sum = 0;
            checkboxes.forEach(cb => {
                if(cb.checked) sum += Number(cb.dataset.points || 0);
            });
            totalPoints += sum;
            currentQuestion++;
            loadQuestion();
        };

        quiz.appendChild(form);
        quiz.appendChild(nextBtn);
        return;
    }

    // Default single-choice handling
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
        message = "You’re managing your business efficiently—maybe a VT could help with overflow tasks.";
    } else if(totalPoints <= 40){
        tier = "Moderate Need";
        message = "You could save 5–10 hours/week with a Virtual Teammate!";
    } else if(totalPoints <= 60){
        tier = "High Need";
        message = "You could save 10–20+ hours/week—enough to focus on growth or clients!";
    } else {
        tier = "Urgent Need";
        message = "A Virtual Teammate could transform your business—20+ hours/week saved and less stress!";
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
    const leadsource = "Virtual Teammate Quiz - Business Owner";
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
Based on your responses, your business is currently losing approximately ${hoursSaved} hours per week to repetitive tasks.

This represents an estimated $${revenuePotential} in recoverable weekly revenue opportunity.

Key Optimization Areas:
• Scheduling & appointment optimization
• Automated client follow-ups & lead nurturing
• CRM data entry & record management
• Standardized process documentation & SOPs
• Reporting, dashboards & KPI tracking

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
                { name: "lead_source", value: leadsource }
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

// Fire-and-forget POST to the site lead endpoint (saves to the portal Leads DB).
const LEAD_URL = "<?= $home_base ?>lead.php";
function submitToLeadDB(email, phone, tier, hoursSaved, revenuePotential){
    const fd = new URLSearchParams();
    fd.append("email", email || "");
    fd.append("phone", phone || "");
    fd.append("source", "Business Efficiency Quiz");
    fd.append("form", "business-quiz");
    fd.append("message",
        "Quiz result: " + tier + " (" + totalPoints + " pts). " +
        "Est. " + hoursSaved + " hrs/week reclaimable, ~$" + revenuePotential + "/week in recoverable revenue.");
    fetch(LEAD_URL, { method: "POST", body: fd, credentials: "same-origin" })
        .then(function(r){ if(!r.ok){ console.error("Lead DB submission error"); } })
        .catch(function(e){ console.error("Lead DB submission failed:", e); });
}


loadQuestion();
</script>

<?php $hide_lead_band = true; /* the quiz is the page's lead capture */ ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
