using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Diagnostics;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using System.Timers;
using System.Windows.Forms;


using System.Collections;

namespace E3DSchedule
{
    public partial class FormE3DSchedule : Form
    {
        private string[] arguments;
        private Process batchProcess;
        private CancellationTokenSource cancellationTokenSource;

        private int moduleNumber;
        private bool isStanadalone;

        private Dictionary<string, int> modName;

        public FormE3DSchedule(string[] args)
        {
            InitializeComponent();

            arguments = args;
            scheduleTime.Value = DateTime.Today.AddHours(23).AddMinutes(30);

            SetStateStop();
            
            modName = new Dictionary<string, int>
            {
                { "DESIGN", 78 },
                { "DRAW", 40 },
                { "ISO", 33 },
                { "PARAGON", 81 }
            };

            foreach (var kvp in modName)
            {
                listBoxModule.Items.Add(kvp.Key); // Add only the key to the ListBox
            }

            listBoxModule.SelectedIndex = 0;

            isStanadalone = false;
            ReadEnvironment();
        }
        
        private void SetStateRun()
        {
            listBoxModule.Enabled = false;
            scheduleTime.Enabled = false;
            buttonRun.Text = "Stop";

            buttonRun.Enabled = true;
            buttonRun.BackColor = Color.Orange;
            buttonRun.ForeColor = SystemColors.ControlText;
        }
        private void SetStateStop()
        {
            listBoxModule.Enabled = true;
            scheduleTime.Enabled = true;
            buttonRun.Text = "Start";

            buttonRun.Enabled = true;
            buttonRun.BackColor = Color.Lime;
            buttonRun.ForeColor = SystemColors.ControlText;
        }

        private async void ButtonRun_ClickAsync(object sender, EventArgs e)
        {
            if(buttonRun.Text == "Start")
            {
                MessageLog("Start schedule");
                SetStateRun();
                await RunScheduleAsync();
            }
            else
            {
                MessageLog("Stop schedule");
                SetStateStop();
                StopSchedule();
            }
            
        }

        private async Task RunScheduleAsync()
        {
            Console.WriteLine("runSchedule");

            TimeSpan TimeSpan = scheduleTime.Value - DateTime.Now;

            Console.WriteLine(TimeSpan.TotalMilliseconds);

            int timeSleep = (int)TimeSpan.TotalMilliseconds;
            if(timeSleep < 0)
            {
                timeSleep = 1000;
            }
            cancellationTokenSource = new CancellationTokenSource();

            try
            {
                await Task.Delay(timeSleep, cancellationTokenSource.Token);

                RunBatchFile();
            }
            catch (TaskCanceledException)
            {
                Console.WriteLine("Task was canceled.");
                SetStateStop();
            }
        }

        private void StopSchedule()
        {
            if (batchProcess != null && !batchProcess.HasExited)
            {
                batchProcess.Kill();
                batchProcess.Dispose();
                batchProcess = null;
            }

            // Cancel the Task.Delay if it's still waiting
            if (cancellationTokenSource != null)
            {
                cancellationTokenSource.Cancel();
                cancellationTokenSource.Dispose();
                cancellationTokenSource = null;
            }

            SetStateStop();
        }

        private void RunBatchFile()
        {
            // Run the batch process
            if (batchProcess == null || batchProcess.HasExited)
            {
                MessageLog("Run");

                buttonRun.ForeColor = SystemColors.ControlLightLight;
                buttonRun.BackColor = Color.Red;
                buttonRun.Text = "Run";

                buttonRun.Enabled = false;

                moduleNumber = modName[listBoxModule.SelectedItem.ToString()];

                Thread processThread = new Thread(async () => await StartE3DStandalone());
                processThread.Start();
            }
        }


        private async Task StartE3DStandalone()
        {
            string parameters = moduleNumber + " \"" + string.Join("\" \"", arguments) + "\"";

            // MessageLog(parameters);

            string applicationPath = "E3DScheduleStandalone.exe";

            ProcessStartInfo startInfo = new ProcessStartInfo
            {
                FileName = applicationPath,
                Arguments = parameters,
                UseShellExecute = false,
                CreateNoWindow = true
            };

            try
            {
                using (Process process = Process.Start(startInfo))
                {
                    await Task.Run(() => process.WaitForExit());

                    this.Invoke((MethodInvoker)delegate {
                        MessageLog("Done");
                        MessageLog("=============");
                        buttonRun.Enabled = true;
                        SetStateStop();
                    });
                }
            }
            catch (Exception ex)
            {
                MessageLog("Error");
                MessageLog("=============");
                buttonRun.Enabled = true;
                SetStateStop();
                MessageBox.Show("Error: " + ex.Message);
            }
        }


        private void MessageLog(string msg)
        {
            DateTime dt = DateTime.Now;
            richTextBoxOutput.AppendText(dt.ToLocalTime().ToString() + " > " + msg + "\r\n");
            richTextBoxOutput.ScrollToCaret();
        }



        private void ReadEnvironment()
        {
            IDictionary environmentVariables = Environment.GetEnvironmentVariables();

            bool isFound = false;
            foreach (DictionaryEntry entry in environmentVariables)
            {
                // MessageLog($"{entry.Key} = {entry.Value}");

                string k = (string)entry.Key;
                string v = (string)entry.Value;

                if (k.Contains("000ID") && v != "ProjACP")
                {
                    // MessageLog(k + " " + v);

                    if(k.Replace("000ID", "") == arguments[0])
                    {
                        isFound = true;
                        this.Text += " - " + v;
                        break;
                    }
                }
            }
            
            if( !isFound )
            {
                listBoxModule.SelectedIndex = -1;
                listBoxModule.Enabled = false;
                buttonRun.Enabled = false;
                scheduleTime.Enabled = false;
                MessageLog("Not found Project, contact Admin");
            }
            // this.projectCode = projectCode;
        }
    }
}
