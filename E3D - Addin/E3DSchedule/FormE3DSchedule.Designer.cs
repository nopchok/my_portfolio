namespace E3DSchedule
{
    partial class FormE3DSchedule
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.buttonRun = new System.Windows.Forms.Button();
            this.scheduleTime = new System.Windows.Forms.DateTimePicker();
            this.richTextBoxOutput = new System.Windows.Forms.RichTextBox();
            this.label3 = new System.Windows.Forms.Label();
            this.listBoxModule = new System.Windows.Forms.ListBox();
            this.SuspendLayout();
            // 
            // buttonRun
            // 
            this.buttonRun.BackColor = System.Drawing.Color.Red;
            this.buttonRun.Enabled = false;
            this.buttonRun.Font = new System.Drawing.Font("Microsoft Sans Serif", 15F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(222)));
            this.buttonRun.ForeColor = System.Drawing.SystemColors.ControlLightLight;
            this.buttonRun.Location = new System.Drawing.Point(176, 48);
            this.buttonRun.Name = "buttonRun";
            this.buttonRun.Size = new System.Drawing.Size(156, 64);
            this.buttonRun.TabIndex = 0;
            this.buttonRun.Text = "Start";
            this.buttonRun.UseVisualStyleBackColor = false;
            this.buttonRun.Click += new System.EventHandler(this.ButtonRun_ClickAsync);
            // 
            // scheduleTime
            // 
            this.scheduleTime.CustomFormat = "dd-MMM HH:mm";
            this.scheduleTime.Font = new System.Drawing.Font("Microsoft Sans Serif", 15F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(222)));
            this.scheduleTime.Format = System.Windows.Forms.DateTimePickerFormat.Custom;
            this.scheduleTime.Location = new System.Drawing.Point(176, 12);
            this.scheduleTime.Name = "scheduleTime";
            this.scheduleTime.ShowUpDown = true;
            this.scheduleTime.Size = new System.Drawing.Size(156, 30);
            this.scheduleTime.TabIndex = 1;
            // 
            // richTextBoxOutput
            // 
            this.richTextBoxOutput.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle;
            this.richTextBoxOutput.Location = new System.Drawing.Point(12, 131);
            this.richTextBoxOutput.Name = "richTextBoxOutput";
            this.richTextBoxOutput.Size = new System.Drawing.Size(320, 130);
            this.richTextBoxOutput.TabIndex = 5;
            this.richTextBoxOutput.Text = "";
            this.richTextBoxOutput.WordWrap = false;
            // 
            // label3
            // 
            this.label3.AutoSize = true;
            this.label3.BackColor = System.Drawing.SystemColors.Control;
            this.label3.Location = new System.Drawing.Point(9, 115);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(50, 13);
            this.label3.TabIndex = 6;
            this.label3.Text = "Message";
            // 
            // listBoxModule
            // 
            this.listBoxModule.Font = new System.Drawing.Font("Microsoft Sans Serif", 14F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(222)));
            this.listBoxModule.FormattingEnabled = true;
            this.listBoxModule.ItemHeight = 24;
            this.listBoxModule.Location = new System.Drawing.Point(13, 12);
            this.listBoxModule.Name = "listBoxModule";
            this.listBoxModule.Size = new System.Drawing.Size(157, 100);
            this.listBoxModule.TabIndex = 7;
            // 
            // FormE3DSchedule
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(344, 274);
            this.Controls.Add(this.listBoxModule);
            this.Controls.Add(this.label3);
            this.Controls.Add(this.richTextBoxOutput);
            this.Controls.Add(this.scheduleTime);
            this.Controls.Add(this.buttonRun);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.MaximizeBox = false;
            this.Name = "FormE3DSchedule";
            this.Text = "E3DSchedule";
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Button buttonRun;
        private System.Windows.Forms.DateTimePicker scheduleTime;
        private System.Windows.Forms.RichTextBox richTextBoxOutput;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.ListBox listBoxModule;
    }
}

